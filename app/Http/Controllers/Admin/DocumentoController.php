<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use App\Models\Assunto;
use App\Models\Documento;
use App\Models\PalavraChave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class DocumentoController extends Controller
{

    /**
     * @var Elastic\Elasticsearch\Client
     */
    private $client;

    /**
     * @param Client $client
    */
    public function __construct()
    {

        $hosts = [        
            getenv('ELASTIC_URL')
        ];
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    public function index(){
        $unidade = auth()->user()->unidade;


        if(auth()->user()->isAdmin()){
            $documentos = Documento::with('tipoDocumento','user','unidade')
            ->simplePaginate(10);
        }else{
            $documentos = Documento::with('tipoDocumento','user','unidade')
            ->where('unidade_id',$unidade->id)
            ->simplePaginate(10);
        }

        

        return view('admin.documento.index', compact('documentos'));
    }

    public function create(){
        $user = auth()->user();

        if($user->isAssessor()){
            return redirect()->route('home')
            ->with('error', 'Como assessor, você não pode gerenciar documentos.');            
        }else{
            $unidade = auth()->user()->unidade;

            $tiposDocumento = TipoDocumento::all();

            $assuntos = Assunto::all();         

            return view('admin.documento.create', compact('unidade','tiposDocumento',  'assuntos'));        
        }         
    }

    public function store(Request $request, Documento $documento){

        try{

            $validator = Validator::make($request->all(), $documento->rules, $documento->messages);
             
            if ($validator->fails()) {
                 return redirect()->back()->withInput()->withErrors($validator);
            }

            $data= $request->all();
         
            $documento = new Documento();
            $documento->fill($data);

            $documento->data_envio = new \DateTime();

            $documento->unidade()->associate(auth()->user()->unidade);
            $documento->user()->associate(auth()->user()->id);
    
            if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
    
                DB::beginTransaction();
                $documento->nome_original = $request->arquivo->getClientOriginalName();
                //$extensao = $request->arquivo->extension();
                //$arquivoNome = "{$tituloArquivo}.{$extensao}";
               
                /**url amigável para arquivo */
                $urlArquivo = $documento->urlizer($documento->unidade->sigla."_".$documento->numero);

                $urlArquivo = $urlArquivo."_".uniqid().".pdf";
                $documento->arquivo = $urlArquivo;

                $upload = $request->arquivo->storeAs('uploads', $urlArquivo);

                $documento->completed = $documento->isCompleto();
                $documento->save();
                
                $tags = explode(",", $data["palavras_chave"]);
                if(is_array($tags) && count($tags)>0){
                    foreach ($tags as $t) {
                        if(!empty($t)){
                            $palavra = new PalavraChave();
                            $palavra->tag = substr($t,0,100);
        
                            $palavra->documento()->associate($documento);
                            $documento->palavrasChaves()->save($palavra);
                        }
                    }
                }

                $bodyDocumentElastic = $documento->toElasticObject();
                $arquivoData = file_get_contents($request["arquivo"]);
                $bodyDocumentElastic["data"] = base64_encode($arquivoData);


                $params = [
                    'index' => 'documentos_ifal',
                    'type'  => '_doc',
                    'id'    => $documento->arquivo,
                    'pipeline' => 'attachment', 
                    'body'  => $bodyDocumentElastic
                    
                ];

                $resultElastic = $this->client->index($params);

                if($resultElastic['result'] == 'created'){
                    DB::commit();

                    return redirect()->route('documento', ['id' => $documento->id])
                        ->with('success', 'Documento enviado com sucesso.');
                }else{
                    throw new Exception((string)$resultElastic);
                }

               
                
            }else{
                return redirect()
			    ->back()->withInput()
			    ->with('error', "Insira um anexo de extensão PDF.");
            }

        }catch(Exception $e){

            DB::rollBack();
            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage()." : ".$e->getTraceAsString():
            "Problemas na indexação do documento. Caso o problema persista, entre em contato pelo email normativas@nees.com.br";


            Log::error($e->getFile().' - Linha '.$e->getLine().' - search::'.$e->getMessage());

            
            return redirect()
			    ->back()->withInput()
			    ->with('error', $messageErro);
        }
    }

    public function show($id){
        $documento = Documento::with(['unidade','tipoDocumento','assunto','palavrasChaves'])->find($id);

        //$elasticObject = $documento->toElasticObject();
        return view('admin.documento.show',compact('documento'));
    }

    public function indexar($documentoId){
        try{
            $documento = Documento::find( $documentoId );

            $bodyDocumentElastic = $documento->toElasticObject();
            $pathDocumento = 'uploads/'.$documento->arquivo;

            if(Storage::exists($pathDocumento)){
                $arquivoData = Storage::get($pathDocumento);
                $bodyDocumentElastic["data"] = base64_encode($arquivoData);
    
                $params = [
                    'index' => 'documentos_ifal',
                    'type'  => '_doc',
                    'id'    => $documento->arquivo,
                    'pipeline' => 'attachment', 
                    'body'  => $bodyDocumentElastic
                    
                ];
    
                $result = $this->client->index($params);
                
                if($result['result'] == 'created' || $result['result'] == 'updated'){
                    $documento->status_extrator = Documento::STATUS_EXTRATOR_INDEXADO;
                }else{
                    $documento->status_extrator = Documento::STATUS_EXTRATOR_FALHA_ELASTIC;
                }
            }else{
                $documento->status_extrator = Documento::STATUS_EXTRATOR_CADASTRADO;
                return redirect()
			    ->back()
			    ->with('error', "Arquivo do documento não encontrado.");
            }
            $documento->completed = true;
            $documento->save();

            return redirect()->route('documentos')->with('success', 'Documento atualizado com sucesso.');
        }catch(\Exception $e){
            DB::rollBack();

            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage():
            "Documento não foi ocultado. Caso o problema persista, entre em contato pelo email normativas@ness.com.br";
            
            Log::error($e->getFile().' - Linha '.$e->getLine().' - search::'.$e->getMessage());

            return redirect()
			    ->back()
			    ->with('error', $messageErro);
        }
    }

    public function ocultar($documentoId){
        $documento = Documento::find( $documentoId );

        $params = [
            'index' => 'documentos_ifal',
            'type'  => '_doc',
            'client' => [ 
                'ignore' => 404
            ],
            'id'    => $documento->arquivo,
        ];

        try{
            $result = $this->client->get($params);

            if($result['found']){
                $response = $this->client->delete($params);
            }
            $documento->status_extrator = Documento::STATUS_EXTRATOR_BAIXADO;
            $documento->completed = false;
            $documento->save();

            return redirect()->route('documentos')
                ->with('success', 'Documento removido dos resultados com sucesso.');
        }catch(\Exception $e){
            DB::rollBack();

            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage():
            "Documento não foi ocultado. Caso o problema persista, entre em contato pelo email normativas@ness.com.br";
            
            Log::error($e->getFile().' - Linha '.$e->getLine().' - search::'.$e->getMessage());

            return redirect()
			    ->back()
			    ->with('error', $messageErro);
        }
    }


    public function destroy($id)
    {
        $id = (int)$id;
        try{
            DB::beginTransaction();
            $documento = Documento::with('palavrasChaves')->find($id);

            $documento->palavrasChaves()->delete();
            $documento->delete();

            $params = [
                'index' => 'documentos_ifal',
                'type'  => '_doc',
                'client' => [ 
                    'ignore' => 404
                ],
                'id'    => $documento->arquivo,
            ];

            try{
                $result = $this->client->get($params);

                if($result['found']){
                    $response = $this->client->delete($params);
                }
            }catch(\Exception $e){
                //Tentou excluir um documento que não estava indexado
            }
           
            Storage::delete("uploads/$documento->arquivo");
            
            DB::commit();

            return redirect()->route('documentos')
                ->with('success', 'Documento removido com sucesso.');

        }catch(\Exception $e){
            DB::rollBack();

            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage():
            "Documento não foi excluído. Caso o problema persista, entre em contato pelo email normativas@ness.com.br";
            
            Log::error($e->getFile().' - Linha '.$e->getLine().' - search::'.$e->getMessage());

            
            return redirect()
			    ->back()
			    ->with('error', $messageErro);
        }

    }

    public function edit(Request $request, $documentoId){

        try{
            $documento = Documento::find($documentoId);

            if(!$documento){
                Log::error("404: documentoId:".$documentoId);

                $message = 'Desculpa, tivemos problemas com esse documento.';
                return view('errors.404',  compact('message'));
            }
                

            $unidade = auth()->user()->unidade;

            $tiposDocumento = TipoDocumento::all();

            $assuntos = Assunto::all(); 
            
            $tags = ($documento->palavrasChaves ) ? $documento->palavrasChaves->pluck('tag') : [];
            $tags = str_replace('"', '', $tags);
            $tags = str_replace('[', '', $tags);
            $tags = str_replace(']', '', $tags);
            $alerta = null;
            if(!$documento->isIndexado()){
                $alerta = "AVISO: ";
                $alerta .= $documento->isCadastrado() ? 'Selecione um arquivo para envio e clique no botão atualizar.': '';
                $alerta .= $documento->isBaixado() ? 'Clique em atualizar para publicar esse ato tornado-o disponível a busca.': '';
                $alerta .= $documento->isFalhaDownload() ? 'Selecione um arquivo para concluir a indexação do documento. Durante a extração '. 
                            'não foi possível obter o arquivo no endereço especificado.': '';
                $alerta .= $documento->isFalhaElastic() ? 'O nosso sistema não conseguiu utilizar esse documento para busca na última tentativa. '. 
                            'Caso o problema persista ao atualizar novamente. Veja se o documento é um pdf ou um doc(x) válido. Ou envio outro documento.': '';

            }

            return view('admin.documento.edit', compact('tags','documento','unidade','tiposDocumento',  'assuntos'))->with('alerta',$alerta);
        }catch(\Exception $e){
            throw new Exception($e);
        }
        
    }

    public function update(Request $request, $documentoId){
        $documento = Documento::find($documentoId);

        $data= $request->all();
        $documento->fill($data);

        $tags = explode(",", $data["palavras_chave"]);
        if(is_array($tags) && count($tags)>0){
            $documento->palavrasChaves()->delete(); 
            foreach ($tags as $t) {
                if(!empty($t)){
                    $palavra = new PalavraChave();
                    $palavra->tag = substr($t,0,100);

                    $palavra->documento()->associate($documento);
                    $documento->palavrasChaves()->save($palavra);
                }
            }
        }

        $documento->completed = $documento->isCompleto();
        $documento->save();

        if($documento->completed){
            $bodyDocumentElastic = $documento->toElasticObject();

            if($request->hasFile('arquivo_novo') && $request->file('arquivo_novo')->isValid()){
                $urlArquivo = $documento->urlizer($documento->unidade->sigla."_".$documento->numero);
                $urlArquivo = $urlArquivo."_".uniqid().".pdf";
    
                $arquivoOld = $documento->arquivo;
    
                $documento->arquivo = $urlArquivo;
                $documento->nome_original = $request->arquivo_novo->getClientOriginalName();
                $request->arquivo_novo->storeAs('uploads', $urlArquivo);
                $documento->save();
    
                $arquivoData = file_get_contents($request["arquivo_novo"]);
    
                
                Storage::delete("uploads/$arquivoOld");
    
            }
            else{//atualizar dados novos
                if(Storage::exists('uploads/'.$documento->arquivo)){
                    $arquivoData = Storage::get('uploads/'.$documento->arquivo);
                }elseif($documento->completed){
                    $result = $this->client->get([
                        'index' => 'documentos_ifal',
                        'type' => '_doc',
                        'id' => $documento->arquivo
                    ]);
                        
                    $arquivoData =  $result['_source']['data'];
                }
            }
    
            $bodyDocumentElastic["data"] = base64_encode($arquivoData);
            $params = [
                'index' => 'documentos_ifal',
                'type'  => '_doc',
                'id'    => $documento->arquivo,
                'pipeline' => 'attachment', 
                'body'  => $bodyDocumentElastic
                
            ];
    
            $this->client->index($params);
        }
        
        return redirect()->route('documentos')->with('success', 'Documento atualizado com sucesso.');

    }

    // public function listPrivateDocs(){
    //     $unidadeId = auth()->user()->unidade->id;
        
    //     $documentos = Documento::where('unidade_id', $unidadeId)
    //                             ->where('publico', false)
    //                             ->paginate(20);

    //     return view('admin.documento.privite', compact("documentos"));

    // }
}

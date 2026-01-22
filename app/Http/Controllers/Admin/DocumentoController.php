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
use Carbon\Carbon;

class DocumentoController extends Controller
{

    /**
     * @var Client
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
        $username = getenv('ELASTICSEARCH_USERNAME');
        $password = getenv('ELASTICSEARCH_PASSWORD');
        $this->client = ClientBuilder::create()->setHosts($hosts)->setBasicAuthentication($username, $password)->build();
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

                // Leitura binária e montagem do JSON estrito
                $arquivoConteudo = file_get_contents($request->file('arquivo')->getRealPath());
                $bodyDocumentElastic = $this->montarCorpoBlindado($documento, $arquivoConteudo);

                $params = [
                    'index' => 'documentos_ifal',
                    'type'  => '_doc',
                    'id'    => $documento->arquivo,
                    'pipeline' => 'attachment', 
                    'body'  => $bodyDocumentElastic
                ];

                $resultElastic = $this->client->index($params);

                if(in_array($resultElastic['result'], ['created', 'updated'])){
                    DB::commit();

                    return redirect()->route('documento', ['id' => $documento->id])
                        ->with('success', 'Documento enviado com sucesso.');
                }else{
                    throw new Exception("Erro Elastic: " . json_encode($resultElastic));
                }
 
            }else{
                return redirect()->back()->withInput()->with('error', "Insira um anexo de extensão PDF.");
            }

        }catch(Exception $e){

            DB::rollBack();
            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage()." : ".$e->getTraceAsString():
            "Problemas na indexação do documento. Caso o problema persista, entre em contato pelo email normativas@nees.com.br";

            Log::error($e->getFile().' - Linha '.$e->getLine().' - store::'.$e->getMessage());
            
            return redirect()->back()->withInput()->with('error', $messageErro);
        }
    }

    public function show($id){
        $documento = Documento::with(['unidade','tipoDocumento','assunto','palavrasChaves'])->find($id);
        return view('admin.documento.show',compact('documento'));
    }

    public function indexar($documentoId){
        try{
            $documento = Documento::find( $documentoId );
            $pathDocumento = 'uploads/'.$documento->arquivo;

            if(Storage::exists($pathDocumento)){
                
                // Leitura do arquivo do disco e chamada da função blindada
                $arquivoData = Storage::get($pathDocumento);
                $bodyDocumentElastic = $this->montarCorpoBlindado($documento, $arquivoData);
    
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
            DB::rollBack(); // Nota: O rollback aqui pode ser inócuo se não houver transaction aberta, mas mantive o original.

            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage():
            "Documento não foi reindexado. Caso o problema persista, entre em contato.";
            
            Log::error($e->getFile().' - Linha '.$e->getLine().' - indexar::'.$e->getMessage());

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
            // Delete direto com ignore 404
            $this->client->delete($params);

            $documento->status_extrator = Documento::STATUS_EXTRATOR_BAIXADO;
            $documento->completed = false;
            $documento->save();

            return redirect()->route('documentos')
                ->with('success', 'Documento removido dos resultados com sucesso.');
        }catch(\Exception $e){
            
            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage():
            "Documento não foi ocultado. Caso o problema persista, entre em contato.";
            
            Log::error($e->getFile().' - Linha '.$e->getLine().' - ocultar::'.$e->getMessage());

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
                 $this->client->delete($params);
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
            "Documento não foi excluído. Caso o problema persista, entre em contato.";
            
            Log::error($e->getFile().' - Linha '.$e->getLine().' - destroy::'.$e->getMessage());

            
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
        try {
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
                
                $arquivoConteudo = null;

                if($request->hasFile('arquivo_novo') && $request->file('arquivo_novo')->isValid()){
                    $urlArquivo = $documento->urlizer($documento->unidade->sigla."_".$documento->numero);
                    $urlArquivo = $urlArquivo."_".uniqid().".pdf";
        
                    $arquivoOld = $documento->arquivo;
        
                    $documento->arquivo = $urlArquivo;
                    $documento->nome_original = $request->arquivo_novo->getClientOriginalName();
                    $request->arquivo_novo->storeAs('uploads', $urlArquivo);
                    $documento->save();
        
                    $arquivoConteudo = file_get_contents($request->file('arquivo_novo')->getRealPath());
        
                    Storage::delete("uploads/$arquivoOld");
        
                } else {
                    if(Storage::exists('uploads/'.$documento->arquivo)){
                        $arquivoConteudo = Storage::get('uploads/'.$documento->arquivo);
                    } elseif($documento->completed){
                        // Fallback: tenta recuperar o conteúdo anterior do Elastic se não estiver em disco
                        try {
                            $result = $this->client->get([
                                'index' => 'documentos_ifal',
                                'type' => '_doc',
                                'id' => $documento->arquivo
                            ]);
                            // Decodifica o base64 que veio do Elastic para ser reprocessado
                            $arquivoConteudo = base64_decode($result['_source']['data']);
                        } catch(\Exception $ex) {
                            // Ignora erro de recuperação
                        }
                    }
                }
        
                if ($arquivoConteudo) {
                    $bodyDocumentElastic = $this->montarCorpoBlindado($documento, $arquivoConteudo);

                    $params = [
                        'index' => 'documentos_ifal',
                        'type'  => '_doc',
                        'id'    => $documento->arquivo,
                        'pipeline' => 'attachment', 
                        'body'  => $bodyDocumentElastic
                    ];
        
                    $this->client->index($params);
                }
            }
            
            return redirect()->route('documentos')->with('success', 'Documento atualizado com sucesso.');

        } catch(\Exception $e) {
            Log::error($e->getFile().' - Linha '.$e->getLine().' - update::'.$e->getMessage());
            return redirect()->back()->with('error', 'Erro na atualização.');
        }

    }

    /**
     * Monta o array padronizado para o Elastic com tipagem forte
     */
    private function montarCorpoBlindado(Documento $documento, $conteudoBinarioPDF)
    {
        $documento->load(['unidade', 'tipoDocumento', 'palavrasChaves']);

        return [
            'ato' => [
                'id_persisted'    => $documento->id,
                'numero'          => $documento->numero,
                'ano'             => (int) $documento->ano,
                'titulo'          => $documento->titulo,
                'ementa'          => $documento->ementa,
                'url'             => url('uploads/' . $documento->arquivo),
                'data_publicacao' => $documento->data_publicacao ? Carbon::parse($documento->data_publicacao)->format('Y-m-d') : null,
                'data_indexacao'  => date('Y-m-d'),
                'tipo_doc'        => $documento->tipoDocumento->nome ?? 'Outros',
                'arquivo'         => $documento->arquivo,
                'tags'            => $documento->palavrasChaves->pluck('tag')->toArray() ?? [],
                'tipo_entrada'    => 'individual',
                'publico'         => (bool) $documento->publico, 
                'fonte' => [
                    'orgao'    => $documento->unidade->nome ?? 'IFAL',
                    'sigla'    => $documento->unidade->sigla ?? 'IFAL',
                    'uf'       => 'AL', 
                    'uf_sigla' => 'AL',
                    'esfera'   => 'Federal',
                    'url'      => 'https://www.ifal.edu.br'
                ]
            ],
            'data' => base64_encode($conteudoBinarioPDF)
        ];
    }
}
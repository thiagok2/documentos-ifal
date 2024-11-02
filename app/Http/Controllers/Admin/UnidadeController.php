<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Convite;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use App\Models\Estado;
use App\Models\User;
use App\Models\Unidade;
use App\Models\Documento;
use App\Models\Municipio;
use App\Services\UnidadeQuery;
use Illuminate\Support\Facades\Hash;

class UnidadeController extends Controller
{
    public function index(Request $request){

        $user = auth()->user();
        $ordenarPor = $request->query('ordenarPor') ? $request->query('ordenarPor') : "updated_at";
        $emailCadastrado = $request->has('emailCadastrado') ? $request->query('emailCadastrado') : false;
        $incluirDesabilitados = $request->has('incluirDesabilitados') ? $request->query('incluirDesabilitados') : false;
        $statusConvite = $request->query('statusConvite');

        if($user->isAdmin()){
            $esfera = $request->query('esfera');
            $estado = $request->query('estado');
            $nome = $request->query('nome');

            $clausulas = [];
            //$clausulas[] = ['tipo',Unidade::TIPO_CONSELHO];
            if($esfera){
                $clausulas[] = ['esfera', '=', $esfera];  
            }

            if($nome){
                $clausulas[] = ['nome', 'ilike', '%'.strtoupper($nome).'%'];
            }

            if($estado){
                $clausulas[] = ['estado_id', $estado];
            }

            if($statusConvite){
                $clausulas[] = [$statusConvite,'<>', null];
            }

            if($emailCadastrado){
                $clausulas[] = ['email','not like','alterar_email%'];
            }

            if($incluirDesabilitados)
                $unidadesQuery = Unidade::withTrashed()->where($clausulas);
            else
                $unidadesQuery = Unidade::where($clausulas);

            $unidades = $unidadesQuery->with('estado','municipio' ,'responsavel')->orderBy($ordenarPor, 'desc')->paginate(25);
            $estados = Estado::orderBy('nome', 'asc')->get();

            return view('admin.unidade.index', compact('estados','unidades','esfera','estado','nome','emailCadastrado','statusConvite','ordenarPor','incluirDesabilitados'));

        }
        else if($user->isAssessor()){            
            if ($user->unidade->esfera == Unidade::ESFERA_FEDERAL){
                $estado = $request->query('estado');
                $estados = Estado::all();                            
            }            
            else{
                $estado = $user->unidade->estado_id;
                $estados = Estado::find([$estado]);
            }            

            $nome = $request->query('nome');

            $clausulas = [];
            //$clausulas[] = ['tipo',Unidade::TIPO_CONSELHO];            
            $esfera = "Municipal";
            $clausulas[] = ['esfera', '=', $esfera];

            if($nome){
                $clausulas[] = ['nome', 'ilike', '%'.strtoupper($nome).'%'];
            }

            if($estado){
                $clausulas[] = ['estado_id', $estado];
            }   
            
            if($statusConvite){
                $clausulas[] = [$statusConvite,'<>', null];
            }

            if($emailCadastrado){
                $clausulas[] = ['email','not like','alterar_email%'];
            }

            $unidades = Unidade::where($clausulas)->with('estado','municipio' ,'responsavel')->orderBy($ordenarPor,'desc')->paginate(25);
             
            return view('admin.unidade.index', compact('estados','unidades','esfera','estado','nome','emailCadastrado','statusConvite','ordenarPor'));
        }else{
            $unidade = auth()->user()->unidade;
            $users = User::where("unidade_id", $unidade->id)->paginate(25);
            $documentos = Documento::where('unidade_id',$unidade->id)->paginate(25);
            return view('admin.unidade.edit', compact('unidade','users','documentos'));
        } 

    }

    public function show($id){

        $unidadQuery = new UnidadeQuery();
        $statusExtrator = $unidadQuery->documentosEtlPorStatus($id);

        $unidade = Unidade::withTrashed()->with('responsavel')->find($id);

        $documentosCount = Documento::where('unidade_id',$unidade->id)->count();
        $documentosPendentesCount = Documento::where([
            ['completed', false],
            ['unidade_id', $unidade->id]
        ])->count();

        $documentos = $documentosPendentesCount = Documento::where([
            ['unidade_id', $unidade->id]
        ])->count();

        $users = User::withTrashed()->where("unidade_id", $id)->get();

        $alerta = null;
        if (!$unidade->confirmado){
            $alerta = "Dados do conselho ainda não foram confirmados pelo seu gestor.";

            $alerta = ($unidade->convidado_em) ? $alerta." Convite enviado em ".$unidade->convidado_em : $alerta; 

        }

        return view('admin.unidade.show', compact('unidade', 'statusExtrator','documentos','documentosCount','documentosPendentesCount','users','alerta'));

    }

    public function create(Request $request){
        if(auth()->user()->isAdmin()){
            $estados = Estado::all();
        }
        else{
            $estado = auth()->user()->unidade->estado_id;
            $estados = Estado::find([$estado]);
        }
        $unidade = new Unidade();

        return view('admin.unidade.create', compact('estados','unidade'));
    }


    public function edit($id){
        $unidade = Unidade::withTrashed()->find($id);

        $alerta = null;
        if (!$unidade->confirmado){
            $alerta = "Dados do conselho ainda não foram confirmados pelo seu gestor.";
        }

        $users = User::withTrashed()->where("unidade_id", $id)->get();

        $documentos = Documento::where('unidade_id', $id)
            ->orderBy('ano', 'desc')
            ->paginate(10);

        return view('admin.unidade.edit', compact('unidade','users', 'documentos','alerta'));
    }

    public function save(Request $request, Unidade $unidade){

        $validator = Validator::make($request->all(), $unidade->rules, $unidade->messages);
             
        if ($validator->fails()) {
             return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();


        $data = $request->all();                
        $unidade->fill($data);
        $unidade->estado_id = Estado::where("sigla", $unidade->estado_id)->first()->id;        
        $unidade->user()->associate(auth()->user()->id);        

        if ($request->municipio_id != 99999) {
            $municipio = Municipio::find($request->municipio_id);
            $municipio->criado = true;
            $municipio->save();
        } else {
            $unidade->municipio_id = 804;
        }

        $unidade->save();

        DB::commit();

        return redirect()->route('usuario-new-gestor', ['unidade_id' => $unidade->id])
            ->with('success', 'Unidade cadastrada com sucesso. Agora confirme os dados para criar a conta ao gestor do conselho na plataforma Normativas.');

       
    }

    public function store(Request $request, Unidade $unidade)
    {
        try{

            $validator = Validator::make($request->all(), $unidade->rules, $unidade->messages);
            
           
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $primeiroAcesso = is_null($unidade->confirmado_em);
    
            $unidade = Unidade::withTrashed()->find($request->id);
            $data = $request->all();
    
            $unidade->fill($data);
            
            $unidade->responsavel()->associate(auth()->user());
            
            if(!$unidade->confirmado){
                $unidade->confirmado = true;
                $unidade->confirmado_em = date("Y-m-d H:i:s");
    
            }
    
            $unidade->save();
            DB::commit();
           
    
            if(auth()->user()->confirmado){
                return redirect()->route('unidade-edit', ['id' => $unidade->id])
                    ->with('success', 'Unidade atualizada com sucesso.');
    
            }else{
                return redirect()->route('usuario-edit', ['id' => auth()->user()->id])
                    ->with('success', 'Confirme seus dados e altere a senha.');
            }

        }catch(\Exception $e){

            DB::rollBack();
            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
            
           
            return redirect()->back()->withInput()->with('error', $messageError);
        }
       

    }    

    protected $q;
    public function search(Request $request){
        $q = $request->q;
        $federal = Unidade::where('sigla', '#')->first();

        $query = Unidade::with('estado')->whereIn('esfera',['Estadual','Municipal'])->whereNotIn('tipo',[Unidade::TIPO_ASSESSORIA]);

        if($request->has('q')){
            $this->q = $q;
            $query->whereHas('estado', function($query){
                $query->where('nome', 'ilike', '%'.$this->q.'%');
                $query->orWhere('sigla', 'ilike', '%'.$this->q.'%');
            });
        }    

        $unidades = $query->orderBy('nome', 'asc')->paginate(10);

        $page = $request->query('page', 1);        
        $total_pages = $unidades->lastPage();

        return view('unidades.index', compact('unidades','federal','q','page', 'total_pages'));
    }

    public function page(Request $request, $unidadeUrl){
        $unidade = Unidade::with('estado')->withCount('documentos')->where('friendly_url',$unidadeUrl)->first();

        $tiposTotal = DB::select('select t.id, t.nome as tipo, count(*) as total from documentos d
        inner join tipo_documentos t on t.id = d.tipo_documento_id
        where d.unidade_id = ?
        group by t.id, t.nome', [$unidade->id]);

        
        foreach($tiposTotal as $tipo){
            $documentos["".$tipo->id.""] = Documento::where([['unidade_id',$unidade->id],['tipo_documento_id',$tipo->id]])->orderBy('ano','desc')->paginate(25);
        }

        $isAdmin = false;

        if(auth()->check()){
            $user =  auth()->user();
            $isAdmin = $user->isAdmin() || $user->unidade->id = $unidade->id;
        }
        
        
        return view('unidades.page', compact('unidade','tiposTotal','documentos','isAdmin'));
    }

    public function novoAcesso(Request $request){

        //dd($request);
        $unidade = Unidade::find($request->unidade_id);

        // if(User::where("email",$request->gestor_email)->first()){
        //     return redirect()->back()->withInput()->with('error', "Já existe um usuário com o email: ".$request->gestor_email.". Um novo usuário/email é necessário.");
        // }

        //update conselho
        $unidade->nome = $request->conselho_nome;
        $unidade->sigla = $request->conselho_sigla;
        $unidade->convidado_em = date("Y-m-d H:i:s");
        $unidade->save();

        $gestor = $unidade->responsavel;
        $gestor->name = $request->gestor_nome;
        $gestor->email = $request->gestor_email;

        $gestor->save();

        $passwordRandom = bin2hex(openssl_random_pseudo_bytes(4));
        $gestor->password = Hash::make($passwordRandom);
        $convite = new Convite();
        $convite->enviarNovoUsuario($gestor, $passwordRandom);
        $gestor->save();

        return redirect()->route('unidades')
                ->with(['success'=> "Novo convite enviado para $gestor->name($gestor->email)."]);

    }


    public function destroy($id){
        try{
            DB::beginTransaction();
            $unidade = Unidade::find($id);

            if($unidade){

                $users = User::where("unidade_id", $id)->get();

                $users->each(function ($u, $key) {
                    $u->delete();
                });

                $unidade->delete();
                DB::commit();

                return redirect()->route('unidades')
                            ->with(['success'=> "Unidade".$unidade->nome." desabilitada com sucesso"]);
            }else{
                return redirect()
                    ->back()
                    ->with('error', 'O unidade não encontrada.');
            }
          
        }catch(\Exception $e){
            DB::rollBack();

            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
        
            return redirect()->back()->withInput()->with('error', $messageError);
        }
    }

    public function forceDelete($id){
        try{

            DB::beginTransaction();
            $unidade = Unidade::withTrashed()->find($id);
            $unidade->forceDelete();
            DB::commit();

            return redirect()->route('unidades')
                ->with(['success'=> "Unidade ".$unidade->nome." removida definitivamente!"]);

        }catch(\Exception $e){
            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
        
            return redirect()->back()->withInput()->with('error', $messageError);
        }
    }

    public function restore($id){
        try{
            DB::beginTransaction();
            $unidade = Unidade::withTrashed()->find($id);
            $unidade->restore();
            $unidade->usuarios()->restore();
            DB::commit();

            return redirect()->route('unidade-edit', ['id' => $unidade->id])
                ->with(['success'=> "Unidade ".$unidade->nome." restaurada!"]);

        }catch(\Exception $e){
            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
        
            return redirect()->back()->withInput()->with('error', $messageError);
        }
    }

    public function novoResponsavel($unidadeId, $usuarioId){
        try{
            DB::beginTransaction();
            $unidade = Unidade::find($unidadeId);
            $novoResponsavel = User::find($usuarioId);

            $unidade->responsavel()->associate($novoResponsavel);
            $unidade->save();
            DB::commit();
            
            return redirect()->back()->withInput()
                ->with('success', "Unidade com novo responsável: ".$novoResponsavel->name);
        }catch(\Exception $e){
            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
        
            return redirect()->back()->withInput()->with('error', $messageError);
        }
    }
}

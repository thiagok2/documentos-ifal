<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Documento;

use Illuminate\Support\Facades\Validator;
use App\Models\Convite;
use App\Models\Unidade;

class UsuarioController extends Controller
{

    public function index(Request $request){
        if($request->id){
            $user = User::withTrashed()->find($request->id);
        }else{
            $user = auth()->user();
        }
    
        $usuarios = User::withTrashed()->where('unidade_id', $user->unidade_id)->get();

        $documentos = Documento::with('tipoDocumento','user','palavrasChaves')->where('user_id',$user->id)->simplePaginate(20);

        return view('admin.usuario.index', compact('user','usuarios','documentos'));
    }

    public function newGestor(Request $request){
        $unidade = Unidade::find($request->unidade_id);
        $usuario = new User();
        $usuario->name = $unidade->contato;
        $usuario->email = (strpos($unidade->email, ';') !== false)  ? trim(explode(',', $unidade->email)[0]) : $unidade->email;
        $usuario->tipo = User::TIPO_GESTOR;
        $usuario->unidade()->associate($unidade);

        $message = "Confirme os dados do responsável";

        return view('admin.usuario.create', compact('unidade', 'usuario'));
    }

    public function edit($id){
        $user = User::withTrashed()->find($id);

        $alerta = null;
        if (!$user->confirmado){
            $alerta = "Usuário ainda não atualizou seus dados.";
        }

        return view('admin.usuario.edit', compact('user','alerta'));
    }
    
    public function search(Request $request){
       
        $q = $request->q;
        $unidadeQ = $request->unidadeQ;
        $ordenarPor = $request->query('ordenarPor') ? $request->query('ordenarPor') : "updated_at";
        $ordemPor = $request->query('ordemPor') ? $request->query('ordemPor') : "desc";
        $incluirDesabilitados = $request->has('incluirDesabilitados') ? $request->query('incluirDesabilitados') : false;

        if($incluirDesabilitados)
            $usuariosQuery = User::withTrashed()->with('unidade');
        else
            $usuariosQuery = User::with('unidade');
        
        $usuariosQuery = $usuariosQuery->whereNotNull('unidade_id')->where('email','not like','%@extrator.com.br')->where('email','not like','alterar_email%');

        if($q){
            $usuariosQuery = $usuariosQuery->
            where(function($query) use ($q) {
                $query->where('name', 'ilike', '%'.$q.'%');
                $query->orWhere('email', 'ilike', '%'.$q.'%');
            });
        }

        if($unidadeQ){
            $usuariosQuery->whereHas('unidade', function($query) use ($unidadeQ){
                $query->where('nome', 'ilike', '%'.$unidadeQ.'%');
                $query->orWhere('sigla', 'ilike', '%'.$unidadeQ.'%');
            });
        }
        

        if($ordenarPor && ($ordenarPor == 'ultimo_acesso_em' ||$ordenarPor == 'convidado_em')){
            $usuariosQuery = $usuariosQuery->whereNotNull($ordenarPor);
        }

        $usuarios = $usuariosQuery->orderBy($ordenarPor, $ordemPor)->paginate(25);
        
        return view('admin.usuario.search', compact('usuarios','q','ordenarPor','ordemPor','unidadeQ','incluirDesabilitados'));
    }


    public function store(Request $request, User $user){
        
        try{

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|confirmed',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $user = User::withTrashed()->find($request->id);
            $primeiroAcesso = is_null($user->confirmado_em);
    
            $data = $request->all();
            $user->fill($data);
            $user->password =  Hash::make($data['password']);
    
            if(!$user->confirmado){
                $user->confirmado = true;
                $user->confirmado_em = date("Y-m-d H:i:s");
            }
    
            $user->save();
            DB::commit();
    
            if($primeiroAcesso){
                return redirect()->route('home')
                    ->with(['success'=> 'Cadastro concluído com sucesso.']);
            }else{
                return redirect()->route('home');
            }
        }catch(\Exception $e){
            DB::rollBack();

            $messageErro = (getenv('APP_DEBUG') === 'true') ? $e->getMessage():
            "Problemas na indexação do documento. Caso o problema persista, entre em contato com os administradores.";

            return redirect()
			    ->back()
			    ->with('error', $messageErro);
        }
        
       
    }

    public function convidar(Request $request){

        $unidadeId = auth()->user()->unidade_id;

        if($request->has('unidade_id')){
            $unidadeId = $request->query('unidade_id');
        }

        $unidade = Unidade::find($unidadeId);
        $usuario = new User();


        return view('admin.usuario.create', compact('unidade','usuario'));
    } 

    public function create(Request $request, User $user){
        try{

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'tipo' => 'required|string|max:20',
                'email' => 'required|string|email|unique:users|max:255'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            DB::beginTransaction();
            $user = new User();
            $data = $request->all();
            $user->fill($data);

            $passwordRandom = bin2hex(openssl_random_pseudo_bytes(4));
            $user->email = trim($request->input('email'));
            $user->password = Hash::make($passwordRandom);

            if(auth()->user()->isGestor()){
                $user->unidade()->associate(auth()->user()->unidade);
            }else{
                $user->unidade_id = $request->unidadeId;
            }
            
            $convite = new Convite();

            $convite->enviarNovoUsuario($user, $passwordRandom);
            $user->convidado_em = date("Y-m-d H:i:s");
            $user->save();

            if($request->has('unidadeId')){
                $unidade = Unidade::find($request->unidadeId);

                if (!$unidade->responsavel){
                    $unidade->responsavel()->associate( $user );
                    $unidade->save();
                }

                if(!$unidade->convidado_em){
                    $unidade->convidado_em = date("Y-m-d H:i:s");
                }
            }

            DB::commit();

            return redirect()->route('unidade-show', ['id' => $user->unidade_id])
                ->with(['success'=> "Usuário ".$user->name." (".$user->email.") convidado!"]);

        }catch(\Exception $e){
            DB::rollBack();

            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
        
            return redirect()->back()->withInput()->with('error', $messageError);
        }

    }

    public function reenviarConvite(Request $request, $id){

        try{
            DB::beginTransaction();

            $user = User::with('unidade')->find($id);

            $unidade = $user->unidade;

            if(!$unidade->convidado_em){
                $unidade->convidado_em = date("Y-m-d H:i:s");
                $unidade->save();
            }
                

            $passwordRandom = bin2hex(openssl_random_pseudo_bytes(4));

            $user->password = Hash::make($passwordRandom);

            $convite = new Convite();

            $convite->enviarNovoUsuario($user, $passwordRandom);
            $user->save();

            DB::commit();
            
            return redirect()->back()->withInput()
                ->with(['success'=> "Novo convite enviado para $user->name($user->email)."]);
        }catch(\Exception $e){
            DB::rollBack();

            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
        
            return redirect()->back()->withInput()->with('error', "ERROR: ".$messageError);
        }
        
    }

    public function destroy($id){

        try{
            DB::beginTransaction();
            $user = User::find($id);
            
            if($user && !$user->isResponsavel()){

                $user->delete();
                DB::commit();

                return redirect()->route('unidade-edit', ['id' => $user->unidade_id])
                        ->with(['success'=> "Usuário ".$user->name." removido com sucesso!"]);

            }else{
                return redirect()
                    ->back()
                    ->with('error', 'O usuário '.$id.' não pode ser removido');
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
            $user = User::withTrashed()->find($id);
            $user->forceDelete();
            DB::commit();

            return redirect()->route('unidade-edit', ['id' => $user->unidade_id])
                ->with(['success'=> "Usuário ".$user->name." removido permanentemente!"]);

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
            $user = User::withTrashed()->find($id);
            $user->restore();
            DB::commit();

            return redirect()->route('unidade-edit', ['id' => $user->unidade_id])
                ->with(['success'=> "Usuário ".$user->name." restaurado com sucesso!"]);
        }catch(\Exception $e){
            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
        
            return redirect()->back()->withInput()->with('error', $messageError);
        }
    }
}

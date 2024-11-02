<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Convite;
use App\Models\Estado;
use App\Models\Unidade;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AssessoriaController extends Controller
{

    public function index(Request $request){

        $user = auth()->user();

        if($user->isAdmin()){
           
            $estado = $request->query('estado');
            $clausulas = [];
            if($estado){
                $clausulas[] = ['estado_id', $estado];
            }
            $clausulas[] = ['tipo',Unidade::TIPO_ASSESSORIA];
            $unidades = Unidade::where($clausulas)->with('estado','municipio')->paginate(25);
            $estados = Estado::all();

            return view('admin.assessoria.index', compact('estados','unidades','estado'));

        }else{
            return redirect()->route('home')
                ->with('error', 'Apenas os administradores acessam o gerencimemento de assessores.');
        } 

    }

    public function create(Request $request){
        $estados = Estado::where("possui_assessoria", false)->get();
        $unidade = new Unidade();

        return view('admin.assessoria.create', compact('estados','unidade'));
    }

    public function store(Request $request, Unidade $unidade){
       
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'contato' => 'required|string|max:255',
            'estado_id' => 'required|string|exists:estados,sigla',
            'municipio_id' => 'required|integer|exists:municipios,id',
            'telefone' => 'string|max:100',
            'email' => 'unique:users|email'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $unidade = new Unidade();
        $data = $request->all();
        $unidade->fill($data);
        
        $unidade->tipo = Unidade::TIPO_ASSESSORIA;
        $unidade->esfera = Unidade::ESFERA_ESTADUAL;
        $unidade->user()->associate(auth()->user());

        $estado = Estado::where('sigla',$request->estado_id)->first();
        $estado->possui_assessoria = true;
        $estado->save();
        $unidade->estado()->associate($estado);
        $sigla = "assessoria-".strtolower($estado->sigla);
                

        $countAssessoria = Unidade::orWhere([ 
            ["sigla",$sigla],
            ["friendly_url",$sigla]
        ] )->count();
        
        $sigla = $countAssessoria == 0 ? $sigla : $sigla."-".($countAssessoria+1);
        $unidade->sigla = $sigla;
        $unidade->friendly_url = $sigla;

        $passwordRandom = bin2hex(openssl_random_pseudo_bytes(4));
        $gestorAssessoria = User::create([
            'name' => $request->contato,
            'email' => $request->email,
            'password' => Hash::make($passwordRandom),
            'tipo' => User::TIPO_ASSESSOR
        ]);

        $unidade->responsavel()->associate($gestorAssessoria);
        $unidade->save();
        $gestorAssessoria->unidade()->associate($unidade);
        $gestorAssessoria->save();
                
        $convite = new Convite();
        $convite->enviarNovoUsuario($gestorAssessoria, $passwordRandom);
        $gestorAssessoria->save();

        return redirect()->route('assessorias')
                    ->with('success', 'Assessoria cadastrada com sucesso. O assessor receberá um convite por email.');
    }
    
}

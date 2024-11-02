<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Convite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Validator;

use App\Models\Estado;
use App\Models\User;
use App\Models\Unidade;
use App\Models\Documento;
use App\Models\Municipio;
use App\Services\UnidadeQuery;
use Illuminate\Support\Facades\Hash;

class UnidadeConvitesController extends Controller
{
    public function conviteNova(Request $request){
        if(auth()->user()->isAdmin()){
            $estados = Estado::all();
        }
        else{
            $estado = auth()->user()->unidade->estado_id;
            $estados = Estado::find([$estado]);
        }
        $unidade = new Unidade();

        return view('admin.unidade.precreate', compact('estados','unidade'));
    }

    public function convidar(Request $request){
        try{
            $passwordRandom = bin2hex(openssl_random_pseudo_bytes(4));
            $user =  auth()->user();
            DB::beginTransaction(); 
            $data = $request->all();

            $usuarioAcesso = User::where('email', $data['email'])->first();
            if($usuarioAcesso){
                $convite = new Convite();
                $convite->enviarNovoUsuario( $usuarioAcesso, $passwordRandom);

                $usuarioAcesso->convidado_em = date("Y-m-d H:i:s");
                $usuarioAcesso->save();
                
                DB::commit();
                return redirect()->back()
                    ->with(['success'=> 'Um novo convite foi enviado para '.$usuarioAcesso->name.' ('.$data['email'].'). Acesse e confirme os dados.']);

            }

            $municipio = Municipio::find($data['municipio_id']);
            $unidade = Unidade::where('municipio_id',$municipio->id)->first();

            if(!$unidade){ //nova unidade
                $nome_slug = Str::slug($municipio->nome, '-');
                $unidade = Unidade::create([
                    'nome' => 'CONSELHO MUNICIPAL DE EDUCAÇÃO DE '.strtoupper($municipio->nome), 
                    'tipo' => 'Conselho', 
                    'esfera' => 'Municipal',
                    'contato' => $data['nome'],
                    'email' => $data['email'],
                    //'url' => null,
                    'sigla' => 'CME-'.strtoupper($nome_slug)."-".strtoupper($municipio->estado->sigla),
                    'user_id' => $user->id,
                    'estado_id' => $municipio->estado->id,
                    'municipio_id' => $municipio->id,
                    'friendly_url' => strtolower('CME-'.Str::slug($municipio->nome, '-')),
                    'confirmado' => false
                ]);
                
                $gestorMunicipal = User::create([
                    'name' => $data['nome'],
                    'email' => $data['email'],
                    'password' =>  Hash::make($passwordRandom),
                    'tipo' => 'gestor'
                ]);
                
                $municipio->criado = true;
                $unidade->convidado_em = date("Y-m-d H:i:s");
                $municipio->save();
                $unidade->municipio()->associate($municipio);
        
                $unidade->responsavel()->associate($gestorMunicipal);
                $resultUnidade = $unidade->save();
                $gestorMunicipal->unidade()->associate($unidade);
                $gestorMunicipal->save();
                
                $convite = new Convite();
                $convite->enviarNovoUsuario($gestorMunicipal, $passwordRandom);

                $gestorMunicipal->convidado_em = date("Y-m-d H:i:s");
                $gestorMunicipal->save();
                
                DB::commit();
                return redirect()->back()
                    ->with(['success'=> 'Cadastro do conselho iniciado e convite enviado para '.$data['email'].'. Acesse e confirme os dados']);
            }else{//unidade ja existe
                $responsavel = User::find($unidade->responsavel_id);
                $convite = new Convite();
               
                //unidade da base da uncme mas sem dados do gestor
                if(str_starts_with($responsavel->email,'alterar_email') ){
                    $responsavel->email = $data['email'];
                    $responsavel->name = $data['nome'];
                    $responsavel->password = Hash::make($passwordRandom);
                    $responsavel->unidade()->associate($unidade);
                    $responsavel->save();
                    $unidade->convidado_em = date("Y-m-d H:i:s");

                    $unidade->email = $data['email'];
                    $unidade->contato = $data['nome'];
                    $unidade->save();
                    $convite->enviarNovoUsuario($responsavel, $passwordRandom);
                    $responsavel->convidado_em = date("Y-m-d H:i:s");
                    $responsavel->save();
                    DB::commit();

                    return redirect()->back()
                            ->with(['success'=> 'Usuário adicionado ao conselho '.$unidade->nome.'. Um email foi enviado para '.$data['email'].'.']);

                }else{ //novo usuario para unidade ja cadastrada com gestor

                    $result = User::where('email', $data['email'])->get();

                    if($result->isEmpty()){
                        $novoUsuario = User::create([
                            'name' => $data['nome'],
                            'email' => $data['email'],
                            'password' => Hash::make($passwordRandom),
                            'tipo' => 'gestor',
                            'unidade_id' => $unidade->id
                        ]);
                        
                        $unidade->convidado_em = date("Y-m-d H:i:s");
                        $novoUsuario->unidade()->associate($unidade);
                        $novoUsuario->convidado_em = date("Y-m-d H:i:s");
                        $novoUsuario->save();

                        $unidade->save();
                        $convite->enviarNovoUsuario($novoUsuario, $passwordRandom);

                        DB::commit();
                        
                         return redirect()->back()
                            ->with(['success'=> 'Novo usuário adicionado na unidade '.$unidade->nome.'. Convite enviado para'.$data['email'].'.']);
                        
                    }
                }
            }
        
        }catch(\Exception $e){
            DB::rollBack();
            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Operação não foi realizada. Verifique se os dados estão corretos. 
            Caso o problema persista, entre em contato com os administradores.";
            
           
            return redirect()->back()->withInput()->with('error', $messageError);
        }
       
    }
}
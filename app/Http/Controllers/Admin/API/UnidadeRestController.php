<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

use App\Models\Unidade;
use App\User;
use App\Http\Resources\Unidade as UnidadeResource;
use App\Models\Estado;
use App\Models\Municipio;
use App\Services\UnidadeQuery;
use App\Models\Convite;



class UnidadeRestController extends Controller
{

    public function get(){
        return response()->json(Unidade::paginate(15));
    }
    public function evolucaoUnidadesConfirmadas6Meses(){
        $unidadeQuery = new UnidadeQuery();
        $evolucaoUnidadesConfirmadasMes = $unidadeQuery->evolucaoUnidadesConfirmadas6Meses();
    
        return response()->json(
            $evolucaoUnidadesConfirmadasMes
        );
    }

    public function evolucaoUnidadesConfirmadasPeriodo(Request $request){
        $dataInicio = $request->has('data_inicio')? $request->data_inicio : null; 
        $dataFim = $request->has('data_fim')? $request->data_fim : null; 
        
        $unidadeQuery = new UnidadeQuery();
        $evolucaoUnidadesConfirmadasMes = $unidadeQuery->evolucaoUnidadesConfirmadasPeriodo($dataInicio, $dataFim);
    
        return response()->json(
            $evolucaoUnidadesConfirmadasMes
        );
    }

    public function municipios(Request $request, $sigla){
        $somenteNovos = $request->has('somenteNovos')? $request->somenteNovos : false; 
        
        $estado = Estado::where("sigla", strtoupper($sigla))->first();
        $municipios = Municipio::where(
            [
                ["estado_id", $estado->id],
                ["criado", $somenteNovos],
            ]
            )->orderBy('nome', 'asc')->get();

        return response()->json(
            $municipios
        );
    }

    public function municipiosTodos(Request $request, $sigla){
        $estado = Estado::where("sigla", strtoupper($sigla))->first();
        $municipios = Municipio::where( "estado_id", $estado->id )->orderBy('nome','asc')->get();

        return response()->json(
            $municipios
        );

    }

    public function unidade(Request $request, $id){
        
        $unidade = Unidade::find($id);
        $gestor = $unidade->responsavel;
        return response()->json(
            array(
                "unidade" => $unidade,
                "gestor" => $gestor
            )
        );
    }

    public function store(Request $request){

        try{
            DB::beginTransaction(); 
            $data = $request->all();
            $passwordRandom = bin2hex(openssl_random_pseudo_bytes(4));
            $municipio = Municipio::find($data['municipio_id']);
            $unidade = Unidade::where('municipio_id',$municipio->id)->first();
            
            if(!$unidade){ //nova unidade
                $unidade = new Unidade();
                $unidade->fill($data);
                $unidade->user_id = 1;
                

                $gestorMunicipal = User::create([
                    'name' => $data['contato'],
                    'email' => $data['email'],
                    'password' =>  Hash::make($passwordRandom),
                    'tipo' => 'gestor'
                ]);
                
                $municipio->criado = true;
                //$unidade->convidado_em = date("Y-m-d H:i:s");
                $municipio->save();
                $unidade->municipio()->associate($municipio);
        
                $unidade->responsavel()->associate($gestorMunicipal);
                $resultUnidade = $unidade->save();
                $gestorMunicipal->unidade()->associate($unidade);
                $gestorMunicipal->save();
                
                DB::commit();
                
                $convite = new Convite();
                //$convite->enviarNovoUsuario($gestorMunicipal, $passwordRandom);

                return response()->json(
                    array(
                        "unidade" => $unidade,
                    )
                );
            }else{//unidade ja existe
                $responsavel = User::find($unidade->responsavel_id);
                $convite = new Convite();

                if($responsavel->email === $data['email']){//reenvio de convite
                    //$convite->enviarNovoUsuario($responsavel, $passwordRandom);
                }else if(str_starts_with($responsavel->email,'alterar_email') ){
                    $responsavel->email = $data['email'];
                    $responsavel->name = $data['contato'];
                    $responsavel->password = Hash::make($passwordRandom);
                    $responsavel->unidade()->associate($unidade);
                    $responsavel->save();
                    //$unidade->convidado_em = date("Y-m-d H:i:s");

                    
                    $unidade->email = $data['email'];
                    $unidade->contato = $data['contato'];
                    $unidade->save();
                    DB::commit();

                    //$convite->enviarNovoUsuario($responsavel, $passwordRandom);
                }else{ //novo usuario para unidade ja cadastrada com gestor

                    $resultHasEmail = User::where('email', $data['email'])->get();

                    if($resultHasEmail->isEmpty()){
                        $novoUsuario = User::create([
                            'name' => $data['contato'],
                            'email' => $data['email'],
                            'password' =>  Hash::make($passwordRandom),
                            'tipo' => 'gestor',
                            'unidade_id' => $unidade->id
                        ]);
                        
                        //$unidade->convidado_em = date("Y-m-d H:i:s");
                        $novoUsuario->unidade()->associate($unidade);
                        $novoUsuario->save();

                        $unidade->save();
                        DB::commit();
                    }
                    //$convite->enviarNovoUsuario($novoUsuario, $passwordRandom);

                }

                return response()->json(
                    array(
                        "unidade" => $unidade,
                    )
                );
            }
            
            
        }catch(\Exception $e){
            return response()->json(
                array(
                    'message' => $e->getMessage(), 
                    'trace' => $e->getTraceAsString(),
                    'code' => $e->getFile().'('.$e->getLine().')'
                    ) , 500);
        }

    }

 
}

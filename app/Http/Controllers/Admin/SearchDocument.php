<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Documento;

class SearchDocument extends Controller
{
    protected $queryParams = [];

    public function search(Request $request){
        $user = auth()->user();

        if($user->isAssessor()){
            return redirect()->route('home')
            ->with('error', 'Como assessor, você não pode gerenciar documentos.');            
        }else{
            $this->queryParams['unidadeQuery'] = $request['unidadeNome'];

            $this->queryParams['usuarioNome'] = $request['usuarioNome'];

            $this->queryParams['dataInicioEnvio'] = $request['dataInicioEnvio'];
            $this->queryParams['dataFimEnvio'] = $request['dataFimEnvio'];

            $this->queryParams['dataInicioPublicacao'] = $request['dataInicioPublicacao'];
            $this->queryParams['dataFimPublicacao'] = $request['dataFimPublicacao'];

            $this->queryParams['numero'] = $request['numero'];
            $this->queryParams['arquivo'] = $request['arquivo'];

            $this->queryParams['tipo_entrada'] = $request['tipo_entrada'];
            $this->queryParams['status'] = $request['status'];

            $this->queryParams['formato'] = $request['formato'];

            $list = Documento::query();
            
            if(isset($this->queryParams['unidadeQuery'])){
            $list->whereHas('unidade', function($query){
                    $query->withTrashed();
                    $query->where('nome', 'ilike', '%'.$this->queryParams['unidadeQuery'].'%');
                    $query->orWhere('sigla', 'ilike', '%'.$this->queryParams['unidadeQuery'].'%');
                });
            }
            if(isset($this->queryParams['usuarioNome'])){
                $list->whereHas('user', function($query){
                    $query->withTrashed();
                    $query->where('name', 'ilike', '%'.$this->queryParams['usuarioNome'].'%');
                    $query->orWhere('email', 'ilike', '%'.$this->queryParams['usuarioNome'].'%');
                });
            }

            if(isset($this->queryParams['dataInicioEnvio'])){
                $list->whereDate('data_envio','>=',$this->queryParams['dataInicioEnvio']);
            }

            if(isset($this->queryParams['dataFimEnvio'])){
                $list->where('data_envio','<=',$this->queryParams['dataFimEnvio']);
            }

            if(isset($this->queryParams['dataInicioPublicacao'])){
                $list->where('data_publicacao','>=',$this->queryParams['dataInicioPublicacao']);
            }

            if(isset($this->queryParams['dataFimPublicacao'])){
                $list->where('data_publicacao','<=',$this->queryParams['dataFimPublicacao']);
            }

            if(isset($this->queryParams['numero'])){
                $list->where('numero','ilike','%'.$this->queryParams['numero'].'%');
            }

            if(isset($this->queryParams['arquivo'])){
                $list->where('nome_original','ilike','%'.$this->queryParams['arquivo'].'%');
            }

            if(isset($this->queryParams['tipo_entrada'])){

                if($this->queryParams['tipo_entrada'] == 'manual'){
                    $list->where('tipo_entrada','!=', Documento::ENTRADA_EXTRATOR);
                }else{
                    $list->where('tipo_entrada','=', Documento::ENTRADA_EXTRATOR);
                }
            }

            if(isset($this->queryParams['status'])){  
                $list->where('completed', $this->queryParams['status'] == 'indexado');
            }

            if(isset($this->queryParams['formato'])){
                $list->where('formato', $this->queryParams['formato']);
            }

            $listFormatos = Documento::select('formato')->distinct()->groupBy('formato')->get();


            $unidadeUser = auth()->user()->unidade;
            if(!auth()->user()->isAdmin()){
                $list->where('unidade_id',$unidadeUser->id);
            }            

            $documentos = $list->paginate(25);

            $queryParams = $this->queryParams;

            return view('admin.documento.index', compact('documentos','queryParams','listFormatos'));
        }
    }
    public function searchPrivate(Request $request){
        $user = auth()->user();

        if($user->isAssessor()){
            return redirect()->route('home')
            ->with('error', 'Como assessor, você não pode gerenciar documentos.');            
        }else{
            $this->queryParams['usuarioNome'] = $request['usuarioNome'];

            $this->queryParams['dataInicioEnvio'] = $request['dataInicioEnvio'];
            $this->queryParams['dataFimEnvio'] = $request['dataFimEnvio'];

            $this->queryParams['dataInicioPublicacao'] = $request['dataInicioPublicacao'];
            $this->queryParams['dataFimPublicacao'] = $request['dataFimPublicacao'];

            $this->queryParams['numero'] = $request['numero'];
            $this->queryParams['arquivo'] = $request['arquivo'];

            $this->queryParams['tipo_entrada'] = $request['tipo_entrada'];
            $this->queryParams['status'] = $request['status'];

            $this->queryParams['formato'] = $request['formato'];

            $list = Documento::query();

            $list->where('publico', false);
            
            if(isset($this->queryParams['usuarioNome'])){
                $list->whereHas('user', function($query){
                    $query->withTrashed();
                    $query->where('name', 'ilike', '%'.$this->queryParams['usuarioNome'].'%');
                    $query->orWhere('email', 'ilike', '%'.$this->queryParams['usuarioNome'].'%');
                });
            }

            if(isset($this->queryParams['dataInicioEnvio'])){
                $list->whereDate('data_envio','>=',$this->queryParams['dataInicioEnvio']);
            }

            if(isset($this->queryParams['dataFimEnvio'])){
                $list->where('data_envio','<=',$this->queryParams['dataFimEnvio']);
            }

            if(isset($this->queryParams['dataInicioPublicacao'])){
                $list->where('data_publicacao','>=',$this->queryParams['dataInicioPublicacao']);
            }

            if(isset($this->queryParams['dataFimPublicacao'])){
                $list->where('data_publicacao','<=',$this->queryParams['dataFimPublicacao']);
            }

            if(isset($this->queryParams['numero'])){
                $list->where('numero','ilike','%'.$this->queryParams['numero'].'%');
            }

            if(isset($this->queryParams['arquivo'])){
                $list->where('nome_original','ilike','%'.$this->queryParams['arquivo'].'%');
            }

            if(isset($this->queryParams['tipo_entrada'])){

                if($this->queryParams['tipo_entrada'] == 'manual'){
                    $list->where('tipo_entrada','!=', Documento::ENTRADA_EXTRATOR);
                }else{
                    $list->where('tipo_entrada','=', Documento::ENTRADA_EXTRATOR);
                }
            }

            if(isset($this->queryParams['status'])){  
                $list->where('completed', $this->queryParams['status'] == 'indexado');
            }

            if(isset($this->queryParams['formato'])){
                $list->where('formato', $this->queryParams['formato']);
            }

            $listFormatos = Documento::select('formato')->distinct()->groupBy('formato')->get();


            $unidadeUser = auth()->user()->unidade;
            $list->where('unidade_id',$unidadeUser->id);
 
            $documentos = $list->paginate(25);

            $queryParams = $this->queryParams;

            return view('admin.documento.private', compact('documentos','queryParams','listFormatos'));
        }
    }

    public function searchStatus(Request $request){
        $user = auth()->user();

        if($user->isAssessor()){
            return redirect()->route('home')
            ->with('error', 'Como assessor, você não pode gerenciar documentos.');            
        }else{       
            $completed = $request['completed'];

            $this->queryParams['unidadeQuery'] = $request['unidadeNome'];
            $this->queryParams['tipo_entrada'] = $request['tipo_entrada'];
            $this->queryParams['status'] = $request['status'];
            $this->queryParams['formato'] = $request['formato'];
            $this->queryParams['completed'] = $request['completed'];

            $list = Documento::query();

            $unidadeUser = auth()->user()->unidade;
            if(!auth()->user()->isAdmin()){
                $list->where('unidade_id',$unidadeUser->id);
            }

            if(isset($this->queryParams['unidadeQuery'])){
                $list->whereHas('unidade', function($query){
                    $query->withTrashed();
                    $query->where('nome', 'ilike', '%'.$this->queryParams['unidadeQuery'].'%');
                    $query->orWhere('sigla', 'ilike', '%'.$this->queryParams['unidadeQuery'].'%');
                });
            }

            if(isset($this->queryParams['tipo_entrada'])){
                $list->where('tipo_entrada', $this->queryParams['tipo_entrada']);  
            }

            if(isset($this->queryParams['status']) && $this->queryParams['status'] != "PENDENTE"){
                $list->where('status_extrator', $this->queryParams['status']);  
            }else{
                $list->where('status_extrator', '<>' , Documento::STATUS_EXTRATOR_INDEXADO);//->orWhere('completed', false); 
            }

            if(isset($this->queryParams['formato'])){
                $list->where('formato', $this->queryParams['formato']);  
            }

            $documentos = $list->paginate(25);

            $listFormatos = $list->select('formato')->distinct()->groupBy('formato')->get();

            $listStatus = $list->select('status_extrator')->distinct()->groupBy('status_extrator')->get();

            $listTipoEntrada = $list->select('tipo_entrada')->distinct()->groupBy('tipo_entrada')->get();

            $queryParams = $this->queryParams;

            return view('admin.documento.search', compact('documentos','queryParams','listStatus', 
                                                            'listTipoEntrada', 'completed','listFormatos'));
        }
    }
}

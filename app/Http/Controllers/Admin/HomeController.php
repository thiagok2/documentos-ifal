<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\Unidade;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Models\PalavraChave;
use Illuminate\Support\Facades\Input;

use App\Services\UnidadeQuery;
use App\Services\DocumentoQuery;
use App\Services\SearchQuery;
use App\Services\UsuarioQuery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{

    protected $usuarioQuery;
    protected $searchQuery;
    protected $documentoQuery;
    protected $unidadeQuery;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->usuarioQuery = new UsuarioQuery();
        $this->searchQuery = new SearchQuery();
        $this->documentoQuery = new DocumentoQuery();
        $this->unidadeQuery = new UnidadeQuery();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public $unidadeId;
    public $estadoId;

    public function index(Request $request)
    { 
        try{
            $unidade_id = auth()->user()->unidade_id;
            $unidade = Unidade::find($unidade_id);
            error_log("estado: ".$unidade->estado_id);
            $this->unidadeId = $unidade->id;
            $this->estadoId = $unidade->estado_id;
            
            $user = auth()->user();
            $user->ultimo_acesso_em = date("Y-m-d H:i:s");
            $user->save();            
            
         
            if(!$unidade->confirmado){
                Log::warning('[home::redirect::unidade-edit] :: !$unidade->confirmado');
                
                return redirect()->route('unidade-edit', ['id' => $unidade->id])
                        ->with('error', 'Confirme os dados da sua unidade.');
            }

            if(!auth()->user()->confirmado){
                Log::warning('[home::redirect::usuario-edit] :: !auth()->user()->confirmado');

                return redirect()->route('usuario-edit', ['id' => auth()->user()->id])
                    ->with('success', 'Confirme seus dados e cadastre uma nova senha.');
            }

            if($user->isAdmin()){

                $documentosCount = DB::table('documentos')->count();
                $usersCount = DB::table('users')->count();
                $documentosPendentesCount = DB::table('documentos')->where('completed', false)->count();
                $documentos = Documento::with('unidade','tipoDocumento','palavrasChaves')->orderBy('data_envio', 'desc')->paginate(10);
                $unidadesNaoConfirmadas = Unidade::where('confirmado',false)->paginate(10);            
                $unidades = Unidade::has('documentos')                            
                    ->withCount('documentos')               
                    ->orderBy('documentos_count', 'desc')
                    ->paginate(10);     

            }else if($user->isAssessor()){
                $documentosCount = DB::table('documentos')
                ->join('unidades', 'documentos.unidade_id', '=', 'unidades.id')
                ->where('unidades.estado_id', $this->estadoId)
                ->count();

                $usersCount = DB::table('users')
                ->join('unidades', 'users.unidade_id', '=', 'unidades.id')
                ->where('unidades.estado_id', $this->estadoId)
                ->count();
                
                $documentosPendentesCount = DB::table('documentos')
                ->join('unidades', 'documentos.unidade_id', '=', 'unidades.id')
                ->where('unidades.estado_id', $this->estadoId)
                ->where('documentos.completed', false)
                ->count();

                $query = Documento::query();
                $query->whereHas('unidade', function($query){
                    $query->where( 'estado_id',$this->estadoId);
                });
                
                $documentos = $query->with('unidade','tipoDocumento','palavrasChaves')->orderBy('data_envio', 'desc')->paginate(10);
                
                
                $query = Unidade::query();
                $query->where('confirmado',false);
                $query->where( 'estado_id',$this->estadoId);
                $unidadesNaoConfirmadas = $query->paginate(10);


                $query = Unidade::query();
                $query->withCount('documentos');
                $query->where( 'estado_id',$this->estadoId);
                $query->orderBy('documentos_count', 'desc');
                $unidades = $query->paginate(10);

                
            }else if($user->isConselho()){
                $documentosCount = DB::table('documentos')
                ->where('unidade_id', $unidade->id)
                ->count();

                $usersCount = DB::table('users')
                ->where('unidade_id', $unidade->id)
                ->count();

                $documentosPendentesCount = DB::table('documentos')
                ->where('completed', false)
                ->where('unidade_id', $unidade->id)
                ->count();

                $documentos = Documento::with('unidade','tipoDocumento','palavrasChaves')
                    ->where('unidade_id',$unidade->id)
                    ->orderBy('data_envio', 'desc')->paginate(10);

                $documentosPendentesExtrator = Documento::with('unidade','tipoDocumento','palavrasChaves')
                    ->where('unidade_id',$unidade->id)
                    ->where('completed', false)                    
                    ->orderBy('data_envio', 'desc')->paginate(10);

                $documentoQuery = new DocumentoQuery();

                $resultQuery = $documentoQuery->documentosDownloadsByUnidade($unidade->id);

                $downloads = $this->arrayPaginator($resultQuery, $request);
                
                Log::warning('[home::view::home2] :: $user->isConselho()');


                return view('home2',compact('documentos','documentosPendentesExtrator',
                    'documentosCount','documentosPendentesCount','usersCount','downloads'));
            }else{
                Log::warning('[home::redirect::usuarios]');
                return redirect()->route('usuarios');            
            }

            /** Indicadores Gestor */
            
            $countUnidadesConfirmadas = Cache::remember('countUnidadesConfirmadas', now()->addSeconds(86400+300), function () {
                return $this->unidadeQuery->countUnidadeConfirmadas();
            });

            $countUnidadesNaoConfirmadas = Cache::remember('countUnidadesNaoConfirmadas', now()->addSeconds(86400+600), function () {
                return $this->unidadeQuery->countUnidadeNaoConfirmadas();
            });

            $porcentagemConfirmadas = number_format(100*$countUnidadesConfirmadas/($countUnidadesConfirmadas + $countUnidadesNaoConfirmadas),2);
            $totalUnidades = $countUnidadesConfirmadas + $countUnidadesNaoConfirmadas;

            $countUnidadesConfirmadas30Dias = Cache::remember('countUnidadesConfirmadas30Dias', now()->addSeconds(86400+900), function () {
                return $this->unidadeQuery->countUnidadeConfirmadasUltimos30dias();
            });
            
            $evolucaoUnidadesConfirmadasMes = Cache::remember('evolucaoUnidadesConfirmadasMes', now()->addSeconds(86400+1200), function () {
                return $this->unidadeQuery->evolucaoUnidadesConfirmadas6Meses();
            });

            $countEnviados30dias = Cache::remember('countEnviados30dias', now()->addSeconds(86400+1500), function () {
                return $this->documentoQuery->countEnviados30dias();
            });
            
            $evolucaoEnviados6Meses = Cache::remember('evolucaoEnviados6Meses', now()->addSeconds(86400+1800), function () {
                return $this->documentoQuery->evolucaoEnviados6Meses();
            });

            $documentosPorTipo = Cache::remember('documentosPorTipo', now()->addSeconds(86400*1), function () {
                return $this->documentoQuery->documentosPorTipos();
            });

            if($request->has("admin")){

                $test = $request->query("admin");

                $totalConsultas = $test == 1 || $test == 0  ? Cache::remember('totalConsultas', now()->addSeconds(86400*2), function () {
                    return $this->searchQuery->countQuery();
                }) : 1;
    
                $totalConsultas3060 = $test == 2 || $test == 0 ? Cache::remember('totalConsultas3060', now()->addSeconds(86400*3), function () {
                    return $this->searchQuery->countQuery3060Dias();
                }): [['total' => 1],['total' => 1]]; 
    
                $denominador = ($totalConsultas3060[1]->total != 0) ? $totalConsultas3060[1]->total : 1;
                $percentConsultas = 100 * (($totalConsultas3060[0]->total - $totalConsultas3060[1]->total) / $denominador);
    
                $topConsultas =  $test == 3 || $test == 0 ? Cache::remember('topConsultas', now()->addSeconds(3600), function () {
                    return $this->searchQuery->topConsultas(100);
                }): 0;
            }else{
                $totalConsultas = 1;
                $totalConsultas3060 = [['total' => 1],['total' => 1]];
                $topConsultas = [];
                $percentConsultas=100;
            }

            $acessosGestores30Dias = Cache::remember('acessosGestores30Dias', now()->addSeconds(86400*4), function () {
                return $this->usuarioQuery->countAcessos30Dias();
            });

            $tags = Cache::remember('tags', now()->addSeconds(86400*5), function () {
                return DB::table('palavra_chaves')
                ->select(DB::raw('count(*) as tag_count, tag'))
                ->where('tag','!=','')
                ->whereNotNull('tag')
                ->groupBy('tag')
                ->orderBy('tag_count', 'desc')
                ->limit(100)
                ->get();
            });


            $tagCount = Cache::remember('tagCount', now()->addSeconds(4800), function () {
                return DB::table('palavra_chaves')->distinct('tag')->count('tag');
            });

            Log::warning('[home::redirect::home]');
            return view('home',compact('documentos',
                'acessosGestores30Dias',
                'topConsultas',
                'totalConsultas', 'totalConsultas3060','percentConsultas',
                            'documentosPorTipo',
                            'evolucaoEnviados6Meses','countEnviados30dias','evolucaoUnidadesConfirmadasMes',
                            'unidadesNaoConfirmadas','countUnidadesConfirmadas30Dias',
                            'countUnidadesConfirmadas','porcentagemConfirmadas',
                            'totalUnidades','documentosCount','documentosPendentesCount','usersCount','tagCount','tags','unidades'));
        
        }catch(\Exception $e){
            Log::warning('Home::index::Exception');
            $messageError = $e;


            /*
            $messageError = getenv('APP_DEBUG') === 'true' ? $e->getMessage():
            "Problemas ao realizar o login. Entre em contato com os administradores da plataforma.";
            */
            Log::error('Home::exception::'.$messageError);
            $message = $messageError;
            return view('errors/500', compact('message'));
            
        }
    }

    public function arrayPaginator($array, $request)
    {
        $page = Request()->get('page', 1);
        $perPage = 20;
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }


    
}

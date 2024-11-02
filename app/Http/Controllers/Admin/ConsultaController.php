<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Consulta;
use App\Http\Controllers\Controller;
use App\Services\UsuarioQuery;


use App\User;

class ConsultaController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();

        $q = $request->query('q');
        $clausulas = [];
        $clausulas[] = ['termos', '!=',''];

        if($q){
            $clausulas[] = ['termos', 'ilike', '%'.strtoupper($q).'%'];
        }

        $consultas = Consulta::where($clausulas)->orderBy('data_consulta','DESC')->paginate(25);


        $clausulas = [];

        return view('admin.consulta.index', compact('consultas', 'q'));
    }

    public function consultasMes(Request $request){
        $usuarioQuery = new UsuarioQuery();

        $resultQuery = $usuarioQuery->countConsultaMes();


        $consultasMes = $this->arrayPaginator($resultQuery, $request);

        return view('admin.consulta.mes', compact('consultasMes'));
    }

    public function public(Request $request){
        $usuarioQuery = new UsuarioQuery();

        $resultQuery = $usuarioQuery->countConsultaMes();

        $consultasMes = $this->arrayPaginator($resultQuery, $request);

        return view('index.consultas', compact('consultasMes'));
    }

    public function arrayPaginator($array, $request)
    {
        $page = Input::get('page', 1);
        $perPage = 20;
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }
}

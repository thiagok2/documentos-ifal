<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('documentos/assuntos/total', 'API\DocumentoRestController@documentosPorAssunto');
Route::get('documentos/tipos/total', 'API\DocumentoRestController@documentosPorTipo');
Route::get('documentos/enviados/6meses', 'API\DocumentoRestController@evolucaoEnviados6Meses');
Route::get('documentos/enviados/periodo', 'API\DocumentoRestController@evolucaoEnviadosPeriodo');

Route::get('unidades/confirmadas/periodo', 'API\UnidadeRestController@evolucaoUnidadesConfirmadasPeriodo');
Route::get('unidades/{sigla}/municipios', 'API\UnidadeRestController@municipios');
Route::get('unidades/{sigla}/municipios-todos', 'API\UnidadeRestController@municipiosTodos');
Route::get('unidades/confirmadas/6meses', 'API\UnidadeRestController@evolucaoUnidadesConfirmadas6Meses');
//Route::resource('usuarios', 'API\\UserRestController');

Route::get('unidades', 'API\UnidadeRestController@get');
Route::post('unidades', 'API\UnidadeRestController@store');
Route::get('unidades/{id}', 'API\UnidadeRestController@unidade');

Route::get('documentos/{documentoId}/indexar', 'API\ElasticDocumentoController@indexar');
Route::post('documentos', 'API\CrawlerController@store');
Route::get('/ping', function () {
    return ['status' => 'pong', 'message' => 'API do Laravel está viva!'];
});

// Nossa rota oficial de busca
Route::get('/search', [SearchController::class, 'search']);
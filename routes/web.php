<?php

use App\Http\Controllers\Admin\AssessoriaController;
use App\Http\Controllers\Admin\AssuntoController;
use App\Http\Controllers\Admin\ConsultaController;
use App\Http\Controllers\Admin\ConviteController;
use App\Http\Controllers\Admin\DocumentoController;
use App\Http\Controllers\Admin\EnvController;
use App\Http\Controllers\Admin\ETLController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\LoteController;
use App\Http\Controllers\Admin\SearchDocument;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\TipoDocumentoController;
use App\Http\Controllers\Admin\UnidadeController;
use App\Http\Controllers\Admin\UnidadeConvitesController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\PrimeiroAcessoController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',          [IndexController::class, 'index'])->name('index');
Route::get('/consultas', [ConsultaController::class, 'public'])->name('consultas-public');
Route::get('/downloads', [IndexController::class, 'downloads'])->name('downloads-public');
Route::get('/conselhos', [UnidadeController::class, 'search'])->name('unidades-search');
Route::get('/conselhos/{url}', [UnidadeController::class, 'page'])->name('unidades-page');
Route::get('/login',           [LoginController::class, 'login']);
Route::get('/normativa/pdf/{normativaId}',  [PDFController::class, 'pdfNormativa'])->name('pdfNormativa');
Route::get('/normativa/view/{normativaId}', [IndexController::class, 'viewNormativa'])->name('viewNormativa');
Route::get('/filter',                       [IndexController::class, 'filter'])->name('filterNormativa');
Route::get('documentos/delete/{arquivoId}', [IndexController::class, 'delete'])->name('delete-elastic');
Route::get('errors/500', function () {
    return view('errors/500');
});
Route::get('errors/404', function () {
    return view('errors/404');
});
Route::get('/primeiro-acesso', [PrimeiroAcessoController::class, 'first'])->name('primeiro-acesso');
Route::post('solicitar-acesso', [PrimeiroAcessoController::class, 'request'])->name('solicitar-acesso');

Route::prefix('admin')->middleware('auth')->namespace('Admin')->group(function(){
    Route::get('getenv', [EnvController::class, 'getenv'])->name('getenv');
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('guia', function () {
        return view('admin/guide');
    })->name('guia');    
    Route::get('convites',              [ConviteController::class, 'index'])->name('convites');
    Route::get('unidades',              [UnidadeController::class, 'index'])->name('unidades');
    Route::Post('unidades',             [UnidadeController::class, 'store'] )->name('unidade-store');
    Route::post('unidades/salvar',      [UnidadeController::class, 'save'])->name('unidade-save');
    Route::post('unidades/novo-acesso', [UnidadeController::class, 'novoAcesso'])->name('unidade-novo-acesso');
    Route::get('unidades/conviteNova',  [UnidadeConvitesController::class, 'conviteNova'])->name('unidade-convite-nova');
    Route::post('unidades/convidar',    [UnidadeConvitesController::class, 'convidar'])->name('unidade-convidar');
    Route::get('unidade/nova',          [UnidadeController::class, 'create'])->name('unidade-create');
    Route::get('unidades/{id}/edit',    [UnidadeController::class, 'edit'])->name('unidade-edit');
    Route::get('unidade/{id}/show',     [UnidadeController::class, 'show'])->name('unidade-show');
    Route::get('unidades/{id}/delete',  [UnidadeController::class, 'destroy'])->name('unidade-delete');
    Route::get('unidades/{id}/force-delete', [UnidadeController::class, 'forceDelete' ])->name('unidade-force-delete');
    Route::get('unidades/{id}/restore',      [UnidadeController::class, 'restore'])->name('unidade-restore');
    Route::get('unidades/gestor/new',        [UsuarioController::class, 'newGestor'])->name('usuario-new-gestor');
    Route::get('unidades/{unidadeId}/novo-responsavel/{usuarioId}', [UnidadeController::class, 'novoResponsavel'])->name('unidade-novo-responsavel');
    Route::get('unidades/assessorias',       [AssessoriaController::class, 'index'])->name('assessoria');
    Route::get('unidades/assessoria/nova',   [AssessoriaController::class, 'create'])->name('assessoria-create');
    Route::post('unidades/assessoria',       [AssessoriaController::class, 'store'])->name('assessoria-store');
    Route::get('tiposdocumento',             [TipoDocumentoController::class, 'index'])->name('tiposdocumento');
    Route::get('assuntos',                   [AssuntoController::class, 'index'])->name('Assuntos');
    Route::get('assuntos/novo',              [AssuntoController::class, 'create'])->name('assuntos-create');
    Route::post('assuntos/salvar',           [AssuntoController::class, 'store'])->name('assunto-store');
    Route::get('assuntos/editar/{id}',       [AssuntoController::class, 'edit'])->name('assunto-edit');
    Route::get('assuntos/delete/{id}',       [AssuntoController::class, 'destroy'])->name('assunto-delete');
    Route::get('assuntos/removidos',         [AssuntoController::class, 'trashed'])->name('assunto-removidos');
    Route::get('assuntos/restaurar/{id}',    [AssuntoController::class, 'restore'])->name('assunto-restore');
    Route::get('tiposdocumento/novo',        [TipoDocumentoController::class, 'create'])->name('tiposdocumento-create');
    Route::post('tiposdocumento/salvar',     [TipoDocumentoController::class, 'store'])->name('tiposdocumento-store');
    Route::get('tiposdocumento/editar/{id}', [TipoDocumentoController::class, 'edit'])->name('tiposdocumento-edit');
    Route::get('tiposdocumento/delete/{id}', [TipoDocumentoController::class, 'destroy'])->name('tiposdocumento-delete');
    Route::get('tiposdocumento/removidos',   [TipoDocumentoController::class, 'trashed'])->name('tiposdocumento-removidos');
    Route::get('tiposdocumento/restaurar/{id}', [TipoDocumentoController::class, 'restore'])->name('tiposdocumento-restore');
    Route::get('documentos',                 [SearchDocument::class, 'search'])->name('documentos');
    Route::get('documentos/pesquisar',       [SearchDocument::class, 'search'])->name('documentos-pesquisar');
    Route::get('documentos/pesquisar/status', [SearchDocument::class, 'searchStatus'])->name('documentos-pesquisar-status');
    Route::get('documentos/publicar',        [DocumentoController::class, 'create'])->name('publicar');
    Route::post('documentos/publicar',       [DocumentoController::class, 'store'])->name('enviar');
    Route::get('documentos/publicar-lote',   [LoteController::class, 'create'])->name('publicar-lote');
    Route::post('docmentos/publicar-lote',   [LoteController::class, 'store'])->name('enviar-lote');
    Route::post('documentos/upload-lote',    [LoteController::class, 'upload'])->name(('upload-lote'));
    Route::post('documentos/update-item-lote/{id}', [LoteController::class, 'updateItemLote'])->name('update-item-lote');
    Route::get('documentos/pendentes',       [LoteController::class, 'documentosPendentes'])->name('docs-pendeste');
    Route::get('documento/{id}',             [DocumentoController::class, 'show'])->name('documento');
    Route::get('documento/{id}/edit',        [DocumentoController::class, 'edit'])->name('documento-edit');
    Route::post('documento/{id}/update',     [DocumentoController::class, 'update'])->name('documento-update');
    Route::delete('documentos/{id}',         [DocumentoController::class, 'destroy'])->name('delete');
    Route::get('documentos/{id}/delete',     [DocumentoController::class, 'destroy'])->name('delete-edit');
    Route::get('lote/upload/{id}/delete',    [LoteController::class, 'destroy'])->name('delete-upload');
    Route::get('documentos/{id}/ocultar',    [DocumentoController::class, 'ocultar'])->name('documento-ocultar');
    Route::get('documento/{id}/indexar',     [DocumentoController::class, "indexar"])->name('documento-indexar');
    Route::get('usuarios/{id}/editar',       [UsuarioController::class, 'edit'])->name('usuario-edit');
    Route::get('usuarios',                   [UsuarioController::class, 'index'])->name('usuarios');
    Route::get('usarios/convidar',           [UsuarioController::class, 'convidar'])->name('usuario-convidar');
    Route::get('usuarios/reenviar-convite/{id}', [UsuarioController::class, 'reenviarConvite'])->name('usuario-reconvidar');
    Route::post('usuarios',                  [UsuarioController::class, 'store'])->name('usuario-store');
    Route::post('usuarios/create',           [UsuarioController::class, 'create'])->name('usario-create');
    Route::get('usuarios/pesquisar',         [UsuarioController::class, 'search'])->name('usuario-search');
    Route::post('usarios/pesquisar',         [UsuarioController::class, 'search'])->name('usuario-search');
    Route::get('usuarios/delete/{id}',       [UsuarioController::class, 'destroy'])->name('usuario-delete');
    Route::get('usuarios/force-delete/{id}', [UsuarioController::class, 'forceDelete'])->name('usuario-force-delete');
    Route::get('usuarios/restore/{id}',      [UsuarioController::class, 'restore'])->name('usuario-restore');
    Route::get('etl/comandos',               [ETLController::class, 'index'])->name('etl-comandos');
    Route::get('etl/log/download/{logFile}', [ETLController::class, 'downloadLog'])->name('download-log');
    Route::get('etl/executar/{script}',      [ETLController::class, 'executarEtl'])->name('etl-executar');
    Route::get('status/index',               [StatusController::class, 'index'])->name('server-status');
    Route::get('consultas',                  [ConsultaController::class, 'index'])->name('consultas');
    Route::get('consultas-mes',              [ConsultaController::class, 'consultasMes'])->name('consultasMes');
});


Auth::routes();







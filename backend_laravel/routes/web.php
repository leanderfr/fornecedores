<?php

use App\Http\Resources\FornecedorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FornecedorController;

// habilita front end de URL diferente consultar o backend atual (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Route::get('fornecedores', FornecedorController::class)->except(
//  ['create', 'show', 'edit']
//);

// daria pra definir melhor as rotas abaixo se eu tivesse tempo

Route::get('fornecedores', 'App\Http\Controllers\FornecedorController@index');
Route::get('fornecedores/{status}/{searchbox}', 'App\Http\Controllers\FornecedorController@index');
Route::get('fornecedores/{status}', 'App\Http\Controllers\FornecedorController@index');
Route::get('fornecedor/{id}', 'App\Http\Controllers\FornecedorController@getById');

// a edicao de registro nao ta dando certo por causa da falta do 'csrf_token()'
// o laravel retorna erro 419 , é necessario passar o token a cada gravacao, mas nao consigo gerar sem ter que usar o blade
// quem gera csrf_token() é o blade, e nao estou usando 

// ja desativei a verificacao de CSRF no middleware mas o erro muda pra codigo HTTP 302, not found
// nao tenho mais tempo, tem solucao mas preciso sair infelizmente!!

Route::patch('fornecedor/{id}', 'App\Http\Controllers\FornecedorController@update');
//***************************************************************************************************

Route::get('usuarios', 'App\Http\Controllers\UsuarioController@index');
Route::get('usuarios/{status}/{searchbox}', 'App\Http\Controllers\UsuarioController@index');
Route::get('usuarios/{status}', 'App\Http\Controllers\UsuarioController@index');
Route::get('usuario/{id}', 'App\Http\Controllers\UsuarioController@getById');


//$router->Get("/fornecedores/{status}/{searchbox}", function($status, $searchbox) use($handlerFornecedores) {  




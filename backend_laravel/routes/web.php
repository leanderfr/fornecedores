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

Route::get('fornecedores', 'App\Http\Controllers\FornecedorController@index');
Route::get('fornecedores/{status}/{searchbox}', 'App\Http\Controllers\FornecedorController@index');
Route::get('fornecedores/{status}', 'App\Http\Controllers\FornecedorController@index');
Route::get('fornecedor/{id}', 'App\Http\Controllers\FornecedorController@getById');

Route::get('usuarios', 'App\Http\Controllers\UsuarioController@index');
Route::get('usuarios/{status}/{searchbox}', 'App\Http\Controllers\UsuarioController@index');
Route::get('usuarios/{status}', 'App\Http\Controllers\UsuarioController@index');
Route::get('usuario/{id}', 'App\Http\Controllers\UsuarioController@getById');


//$router->Get("/fornecedores/{status}/{searchbox}", function($status, $searchbox) use($handlerFornecedores) {  





Route::get('/', function () {
    return view('welcome');
});


<?php

use App\Http\Resources\FornecedorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FornecedorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Route::get('fornecedores', FornecedorController::class)->except(
//  ['create', 'show', 'edit']
//);

Route::get('fornecedores', 'App\Http\Controllers\FornecedorController@index');
Route::get('fornecedores/{status}/{searchbox}', 'App\Http\Controllers\FornecedorController@index');

//$router->Get("/fornecedores/{status}/{searchbox}", function($status, $searchbox) use($handlerFornecedores) {  





Route::get('/', function () {
    return view('welcome');
});


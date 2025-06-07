<?php

use App\Http\Resources\FornecedorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\UsuarioController;

// habilita front end de URL diferente consultar o backend atual (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

//********************************************************************************************
//********************************************************************************************
// status= active / inactive
Route::get('/fornecedores/{status}/{searchbox}', [FornecedorController::class, 'lista']);
Route::get('/fornecedores/{status}', [FornecedorController::class, 'lista']);

Route::get('/fornecedor/{id}', [FornecedorController::class, 'getById']);

Route::patch('/fornecedor/{id}', [FornecedorController::class, 'update']);
Route::patch('/fornecedor/status/{id}', [FornecedorController::class, 'status']);

Route::post('/fornecedor/{id}', [FornecedorController::class, 'post']);

Route::delete('/fornecedor/{id}', [FornecedorController::class, 'delete']);

//********************************************************************************************
//********************************************************************************************
Route::get('/usuarios/{status}/{searchbox}', [UsuarioController::class, 'lista']);
Route::get('/usuarios/{status}', [UsuarioController::class, 'lista']);

Route::get('/usuarios/{id}', [UsuarioController::class, 'getById']);

Route::patch('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::patch('/usuarios/status/{id}', [UsuarioController::class, 'status']);

Route::post('/usuarios/{id}', [UsuarioController::class, 'post']);

Route::delete('/usuarios/{id}', [UsuarioController::class, 'delete']);





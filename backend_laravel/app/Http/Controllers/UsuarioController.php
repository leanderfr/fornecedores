<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    //*********************************************************************************
    //*********************************************************************************
     public function index()
    {

        $status = request()->route('status');
        $searchbox = request()->route('searchbox');

        // prioridade é pesquisar a searchbox
        if ($searchbox!='')  {
          $usuarios = Usuario::where('nome', 'like', "%$searchbox%")->get();
        } 

        // filtra por ativo/inativo se foi passado
        else { 
          $status = request()->route('status');
          if ($status=='active') {
            $usuarios = Usuario::where('active', true)->get();
          }
          else if ($status=='inactive') {
            $usuarios = Usuario::where('active', false)->get();
          }
          else  {
            $usuarios = Usuario::all();
          }

        }

        return UsuarioResource::collection($usuarios);
    }

    //*********************************************************************************
    //*********************************************************************************

    public function store(StoreUsuarioRequest  $request)
    {
        $usuario = Usuario::create($request->validated());
        return new UsuarioResource($usuario);
    }

    //*********************************************************************************
    //*********************************************************************************

    public function update(StoreUsuarioRequest $request, Usuario $usuario)
    {

      $usuario->update($request->validate());
      return new UsuarioResource($usuario);
        
    }

    //*********************************************************************************
    //*********************************************************************************
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response(null, 204);
    }
}

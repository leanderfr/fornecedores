<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
  protected $erros = [   
      'nome' => 'Nome precisa ter entre 5 e 150 caracteres',
  ];

  protected $camposGravar =  [
    'nome' => ['required', 'min:5', 'max:120'],
  ];

  //*********************************************************************************
  //*********************************************************************************
    public function lista()
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

      return response()->json(UsuarioResource::collection($usuarios));
      //return UsuarioResource::collection($usuarios);
  }

  //*********************************************************************************
  //*********************************************************************************
  public function getById()
  {
      $usuario_id = request()->route('id');
      $usuario = Usuario::find($usuario_id);
      //return new UsuarioResource($fornecedor);
      return response()->json(new UsuarioResource($usuario));
  }



  // *************************************************************************************************************
  // *************************************************************************************************************

  public function post(Request $request) {

      $validado = $request->validate($this->camposGravar, $this->erros);
      $rornecedor = Usuario::create($validado);

  }

    //*********************************************************************************
    //*********************************************************************************
    public function update(Request $request)
    {
      $validado = $request->validate($this->camposGravar, $this->erros);
      Usuario::where('id', $request->route('id'))->update($validado);        
    }


  // *************************************************************************************************************
  // *************************************************************************************************************
  public function delete(Request $request) {

      DB::statement('UPDATE usuarios SET deleted_at = now() WHERE id = '.$request->route('id'));
      return 'Usuário excluído com sucesso.';
  }

  // *************************************************************************************************************
  // *************************************************************************************************************
  public function status(Request $request) {

      // inverte situacao
      DB::statement('UPDATE usuarios SET active = ! ifnull(active, 0) WHERE id = '.$request->route('id'));
      return 'Status alterado com sucesso.';
  }




}

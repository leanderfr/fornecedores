<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\FornecedorResource;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FornecedorController extends Controller
{
  protected $erros = [   
      'cnpj' => 'CNPJ inválido',
      'razao_social' => 'Razão Social precisa ter entre 5 e 150 caracteres',
      'logradouro' => 'Rua/av precisa ter entre 5 e 150 caracteres',
      'numero' => 'Rua/av precisa ter entre 2 e 20 caracteres',
      'cep' => 'CEP inválido',
      'bairro' => 'Bairro precisa ter entre 5 e 150 caracteres',
      'cidade' => 'Cidade precisa ter entre 5 e 150 caracteres',
      'uf' => 'Estado precisa ter 2 caracteres',
      'pais' => 'País precisa ter entre 5 e 150 caracteres',
  ];

  protected $camposGravar =  [
    'cnpj' => ['required', 'min:18', 'max:18'],
    'razao_social' => ['required', 'min:5', 'max:150'],
    'logradouro' => ['required', 'min:5', 'max:150'],
    'numero' => ['required', 'min:2', 'max:20'],
    'cep' => ['required', 'min:9', 'max:9'],
    'bairro' => ['required', 'min:5', 'max:150'],
    'cidade' => ['required', 'min:5', 'max:150'],
    'uf' => ['required', 'min:2', 'max:2'],
    'pais' => ['required', 'min:2', 'max:150'],
  ];

  //*********************************************************************************
  //*********************************************************************************
    public function lista()
  {

      $status = request()->route('status');
      $searchbox = request()->route('searchbox');

      // prioridade é pesquisar a searchbox
      if ($searchbox!='')  {
        $fornecedores = Fornecedor::where('razao_social', 'like', "%$searchbox%")->get();
      } 

      // filtra por ativo/inativo se foi passado
      else { 
        $status = request()->route('status');
        if ($status=='active') {
          $fornecedores = Fornecedor::where('active', true)->get();
        }
        else if ($status=='inactive') {
          $fornecedores = Fornecedor::where('active', false)->get();
        }
        else  {
          $fornecedores = Fornecedor::all();
        }

      }

      return response()->json(FornecedorResource::collection($fornecedores));
  }


  //*********************************************************************************
  //*********************************************************************************
  public function getById()
  {
      $fornecedor_id = request()->route('id');
      $fornecedor = Fornecedor::find($fornecedor_id);
      //return new FornecedorResource($fornecedor);
      return response()->json(new FornecedorResource($fornecedor));
  }


  // *************************************************************************************************************
  // *************************************************************************************************************

  public function post(Request $request) {
      $validado = $request->validate($this->camposGravar, $this->erros);

      try {
        Fornecedor::create($validado);
      } catch (\Illuminate\Database\QueryException $exception) {
        $errorInfo = $exception->errorInfo;

        die('aa'.$errorInfo);
      }
  }

  //*********************************************************************************
  //*********************************************************************************
  public function update(Request $request)
  {
    $validado = $request->validate($this->camposGravar, $this->erros);

    try {
        Fornecedor::where('id', $request->route('id'))->update($validado);        
    } catch (\Illuminate\Database\QueryException $exception) {
        $errorInfo = $exception->errorInfo;

        die($errorInfo);
    }

  }


  // *************************************************************************************************************
  // *************************************************************************************************************
  public function delete(Request $request) {

      DB::statement('UPDATE fornecedores SET deleted_at = now() WHERE id = '.$request->route('id'));
      return 'Fornecedor excluído com sucesso.';
  }

  // *************************************************************************************************************
  // *************************************************************************************************************
  public function status(Request $request) {

      // inverte situacao
      DB::statement('UPDATE fornecedores SET active = ! ifnull(active, 0) WHERE id = '.$request->route('id'));
      return 'Status alterado com sucesso.';
  }


}

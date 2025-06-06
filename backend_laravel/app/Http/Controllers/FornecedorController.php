<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFornecedorRequest;
use App\Http\Resources\FornecedorResource;
use App\Models\Fornecedor;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    //*********************************************************************************
    //*********************************************************************************
     public function index()
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
        //return new FornecedorResource($fornecedores);
        //return response()->json(new FornecedorResource($fornecedores));
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


    //*********************************************************************************
    //*********************************************************************************

    public function store(StoreFornecedorRequest  $request)
    {
        $fornecedor = Fornecedor::create($request->validated());
        return new FornecedorResource($fornecedor);
    }

    //*********************************************************************************
    //*********************************************************************************

    // nao consigo fazer update funcionar, desespero, maldito csrf
    public function update(StoreFornecedorRequest $request, Fornecedor $fornecedor)
    {

      $fornecedor->update($request->validate());
      return new FornecedorResource($fornecedor);
        
    }

    //*********************************************************************************
    //*********************************************************************************
    public function destroy(Fornecedor $fornecedor)
    {
        $fornecedor->delete();
        return response(null, 204);
    }
}

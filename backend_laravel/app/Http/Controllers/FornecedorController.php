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

        

        $fornecedores = Fornecedor::where('active', false)->get();
        return FornecedorResource::collection($fornecedores);
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

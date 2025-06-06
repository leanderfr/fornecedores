<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FornecedorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);

        return [
          'id' => $this->id,
          'cnpj' => $this->cnpj,
          'razao_social' => $this->razao_social,
          'logradouro' => $this->logradouro,
          'numero' => $this->numero,
          'cep' => $this->cep,
          'bairro' => $this->bairro,
          'cidade' => $this->cidade,
          'uf' => $this->uf,
          'pais' => $this->pais,
          'created_at' => $this->created_at,
        ];
    }
}

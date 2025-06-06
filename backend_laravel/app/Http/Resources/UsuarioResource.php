<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public static $wrap = null;  // exclui a necessidade de agrupar com a chave 'data'

    public function toArray(Request $request): array
    {
        return [
          'id' => $this->id,
          'nome' => $this->nome,
          'active' => $this->active,
          'created_at' => $this->created_at,
        ];
    }
}

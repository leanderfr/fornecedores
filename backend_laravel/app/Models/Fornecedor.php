<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{

    protected $table = 'fornecedores';
    protected $fillable = [
      'cnpj','razao_social','logradouro','numero','cep','bairro','cidade','uf','pais'
    ] ;
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFornecedorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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

    }
}

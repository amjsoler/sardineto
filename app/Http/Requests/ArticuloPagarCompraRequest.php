<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiUnaCompraYaEstaPagada;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticuloPagarCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(["compraId" => $this->compra->id]);
    }

    public function rules(): array
    {
        return [
            "compraId" => [
                Rule::exists("usuarios_compran_articulos", "id"),
                new ComprobarSiUnaCompraYaEstaPagada()
            ]
        ];
    }
}

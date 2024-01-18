<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiCompraYaEstaEntregada;
use Illuminate\Foundation\Http\FormRequest;

class ArticuloEntregarCompraRequest extends FormRequest
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
                "exists:usuarios_compran_articulos,id",
                new ComprobarSiCompraYaEstaEntregada()
            ]
        ];
    }

    public function messages()
    {
        return [
            "compraId.exists" => "La compra especificada no existe"
        ];
    }
}

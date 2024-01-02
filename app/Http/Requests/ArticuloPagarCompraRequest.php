<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiUnaCompraYaEstaPagada;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticuloPagarCompraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(["compraId" => $this->compra->id]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticuloEditarArticuloRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            "nombre" => "max:150",
            "descripcion" => "max:5000",
            "stock" => "integer|min:0",
        ];
    }

    public function messages()
    {
        return [
            "nombre.max" => __("validation.articulo.nombre.max"),
            "descripcion.max" => __("validation.articulo.descripcion.max"),
            "stock.integer" => __("validation.articulo.stock.integer"),
            "stock.min" => __("validation.articulo.stock.min"),
        ];
    }
}

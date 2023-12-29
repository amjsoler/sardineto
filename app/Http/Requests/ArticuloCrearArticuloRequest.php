<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticuloCrearArticuloRequest extends FormRequest
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
            "nombre" => "required|max:150",
            "descripcion" => "max:5000",
            "stock" => "required|numeric|min:0",
        ];
    }

    public function messages()
    {
        return [
            "nombre.required" => __("validation.articulo.nombre.required"),
            "nombre.max" => __("validation.articulo.nombre.max"),
            "descripcion.max" => __("validation.articulo.descripcion.max"),
            "stock.required" => __("validation.articulo.stock.required"),
            "stock.numeric" => __("validation.articulo.stock.numeric"),
            "stock.min" => __("validation.articulo.stock.min"),
        ];
    }
}

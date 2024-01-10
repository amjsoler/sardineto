<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GimnasioEditarGimnasioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nombre" => "max:150",
            "descripcion" => "nullable|max:5000",
            "logo" => "nullable",
            "direccion" => "nullable|max:200"
        ];
    }

    public function messages(): array
    {
        return  [
            "nombre.max" => __("validation.gimnasio.nombre.max"),
            "descripcion.max" => __("validation.gimnasio.descripcion.max"),
            "direccion.max" => __("validation.gimnasio.direccion.max")
        ];
    }
}

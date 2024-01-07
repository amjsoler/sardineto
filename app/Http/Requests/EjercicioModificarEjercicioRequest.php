<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EjercicioModificarEjercicioRequest extends FormRequest
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
            "nombre" => [
                "string",
                "max:150"
            ],

            "descripcion" => [
                "nullable",
                "string",
                "max:500"
            ],

            "demostracion" => [
                "nullable",
                "url",
            ]
        ];
    }

    public function messages()
    {
        return [
            "nombre.string" => __("validation.ejercicio.nombre.string"),
            "nombre.max" => __("validation.ejercicio.nombre.max"),
            "descripcion.nullable" => __("validation.ejercicio.descripcion.nullable"),
            "descripcion.string" => __("validation.ejercicio.descripcion.string"),
            "descripcion.max" => __("validation.ejercicio.descripcion.max"),
            "demostracion.nullable" => __("validation.ejercicio.demostracion.nullable"),
            "demostracion.url" => __("validation.ejercicio.demostracion.url"),
        ];
    }
}

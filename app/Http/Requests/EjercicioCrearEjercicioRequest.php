<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EjercicioCrearEjercicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nombre" => [
                "required",
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
            "nombre.required" => __("validation.ejercicio.nombre.required"),
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

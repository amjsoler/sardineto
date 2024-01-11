<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EjercicioAsociarEjercicioAClaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge(["gimnasio" => $this->gimnasio->id, "clase" => $this->clase->id, "ejercicio" => $this->ejercicio->id]);
    }

    public function rules(): array
    {
        return [
            "detalles" => [
                "nullable",
                "string",
                "max:100",
            ],
            "ejercicio" => Rule::unique("ejercicios_clases", "ejercicio")->where("gimnasio", $this->gimnasio)->where("clase", $this->clase)
        ];
    }

    public function messages()
    {
        return [
            "detalles.nullable" => __("validation.ejercicio.detalles.nullable"),
            "detalles.string" => __("validation.ejercicio.detalles.string"),
            "detalles.max" => __("validation.ejercicio.detalles.max"),
            "ejercicio.unique" => __("validation.ejercicio.ejercicio.unique"),
        ];
    }
}

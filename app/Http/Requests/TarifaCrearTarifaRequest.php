<?php

namespace App\Http\Requests;

use App\Enums\TiposTarifa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TarifaCrearTarifaRequest extends FormRequest
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
                "max:150"
            ],
            "precio" => [
                "required",
                "decimal:0,2",
                "min:0"
            ],
            "creditos" => [
                "required",
                "integer",
                "min:0"
            ],
            "tipo" => [
                Rule::enum(TiposTarifa::class)
            ]
        ];
    }

    public function messages()
    {
        return [
            "nombre.required" => __("validation.tarifa.nombre.required"),
            "nombre.max" => __("validation.tarifa.nombre.max"),
            "precio.required" => __("validation.tarifa.precio.required"),
            "precio.decimal" => __("validation.tarifa.precio.decimal"),
            "precio.min" => __("validation.tarifa.precio.min"),
            "creditos.required" => __("validation.tarifa.creditos.required"),
            "creditos.integer" => __("validation.tarifa.creditos.integer"),
            "creditos.min" => __("validation.tarifa.creditos.min"),
            "tipo.enum" => __("validation.tarifa.tipo.enum")
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifaCrearTarifaRequest extends FormRequest
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
        ];
    }
}

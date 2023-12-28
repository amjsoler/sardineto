<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifaEditarTarifaRequest extends FormRequest
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
                "max:150"
            ],
            "precio" => [
                "decimal:0,2",
                "min:0"
            ],
            "creditos" => [
                "integer",
                "min:0"
            ]
        ];
    }

    public function messages()
    {
        return [
            "nombre.max" => __("validation.tarifa.nombre.max"),
            "precio.decimal" => __("validation.tarifa.precio.decimal"),
            "precio.min" => __("validation.tarifa.precio.min"),
            "creditos.integer" => __("validation.tarifa.creditos.integer"),
            "creditos.min" => __("validation.tarifa.creditos.min"),
        ];
    }
}

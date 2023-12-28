<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuscripcionCrearSuscripcionComoAdminRequest extends FormRequest
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
            "tarifa" => [
                "required",
                Rule::exists("tarifas", "id")->where("gimnasio", $this->gimnasio->id)
            ],
            "usuario" => [
                "required",
                Rule::exists("usuarios_gimnasios", "usuario")->where("gimnasio", $this->gimnasio->id)
            ]
        ];
    }

    public function messages()
    {
        return [
            "tarifa.required" => __("validation.suscripcion.tarifa.required"),
            "tarifa.exists" => __("validation.suscripcion.tarifa.exists"),
            "usuario.required" => __("validation.suscripcion.usuario.exists"),
            "usuario.exists" => __("validation.suscripcion.usuario.exists"),
        ];
    }
}

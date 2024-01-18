<?php

namespace App\Http\Requests;

use App\Rules\ComprobarQueUserNoTieneSuscripcionEsteMes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuscripcionCrearSuscripcionComoAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(["gimnasio" => $this->gimnasio]);
    }

    public function rules(): array
    {
        return [
            "tarifa" => [
                "required",
                Rule::exists("tarifas", "id")->where("gimnasio", $this->gimnasio->id)
            ],
            "usuario" => [
                "required",
                Rule::exists("usuarios_gimnasios", "usuario")->where("gimnasio", $this->gimnasio->id)->where("invitacion_aceptada", true),
                new ComprobarQueUserNoTieneSuscripcionEsteMes($this->gimnasio->id)
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

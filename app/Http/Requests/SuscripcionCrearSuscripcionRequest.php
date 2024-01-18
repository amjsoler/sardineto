<?php

namespace App\Http\Requests;

use App\Rules\ComprobarQueUserNoTieneSuscripcionEsteMes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuscripcionCrearSuscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(["usuarioQueSeSuscribe" => auth()->user()->id]);
    }

    public function rules(): array
    {
        return [
            "tarifa" => [
                "required",
                Rule::exists("tarifas", "id")->where("gimnasio", $this->gimnasio->id)
            ],
            "usuarioQueSeSuscribe" => [
                new ComprobarQueUserNoTieneSuscripcionEsteMes($this->gimnasio->id)
            ]
        ];
    }

    public function messages()
    {
        return [
            "tarifa.required" => __("validation.suscripcion.tarifa.required"),
            "tarifa.exists" => __("validation.suscripcion.tarifa.exists"),
        ];
    }
}

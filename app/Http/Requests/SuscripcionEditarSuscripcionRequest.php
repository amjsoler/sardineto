<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuscripcionEditarSuscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "tarifa" => [
                Rule::exists("tarifas", "id")->where("gimnasio", $this->gimnasio->id)
            ],

        ];
    }

    public function messages()
    {
        return [
            "tarifa.exists" => __("validation.suscripcion.tarifa.exists"),
        ];
    }
}

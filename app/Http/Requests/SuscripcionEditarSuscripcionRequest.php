<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuscripcionEditarSuscripcionRequest extends FormRequest
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
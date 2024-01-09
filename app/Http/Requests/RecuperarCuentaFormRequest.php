<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecuperarCuentaFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "correo" => "required"
        ];
    }

    public function messages()
    {
        return [
            "correo.required" => __("validation.usuario.correo.required")
        ];
    }
}

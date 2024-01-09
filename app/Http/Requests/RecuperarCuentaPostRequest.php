<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecuperarCuentaPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "password" => "required|confirmed",
            "token" => "required"
        ];
    }

    public function messages()
    {
        return [
            "password.required" => __("validation.usuario.password.required"),
            "password.confirmed" => __("validation.usuario.password.confirmed")
        ];
    }
}

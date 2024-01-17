<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => "required|email|exists:users,email",
            "password" => "required"
        ];
    }

    public function messages()
    {
        return [
            "email" => [
                "required" => __("validation.usuario.email.required"),
                "email" => __("validation.usuario.email.email"),
                "exists" => __("validation.usuario.email.exists"),
            ],
            "password" => [
                "required" => __("validation.usuario.password.required"),
            ]
        ];
    }
}

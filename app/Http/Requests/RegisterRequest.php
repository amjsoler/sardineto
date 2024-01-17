<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "required|max:100",
            "email" => ["required", "email", "unique:users,email"],
            "password" => "required|confirmed",
        ];
    }

    public function messages()
    {
        return [
            "name.required" => __("validation.usuario.name.required"),
            "name.max" => __("validation.usuario.name.max"),

            "email.required" => __("validation.usuario.email.required"),
            "email.email" => __("validation.usuario.email.email"),
            "email.unique" => __("validation.usuario.email.unique"),

            "password.required" => __("validation.usuario.password.required"),
            "password.confirmed" => __("validation.usuario.password.confirmed"),
        ];
    }
}

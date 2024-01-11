<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiUsuarioYaEstaInvitadoAGimnasio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GimnasioInvitarUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => [
                "required",
                "email",
                "exists:users,email",
                new ComprobarSiUsuarioYaEstaInvitadoAGimnasio($this->gimnasio->id)
            ]
        ];
    }

    public function messages() : array
    {
        return [
            "email.required" => __("validation.gimnasio.email.required"),
            "email.email" => __("validation.gimnasio.email.email"),
            "email.exists" => __("validation.gimnasio.email.exists"),
        ];
    }
}

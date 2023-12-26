<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GimnasioInvitarUsuarioRequest extends FormRequest
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
            "email" => [
                "required",
                "email",
                "exists:users,email",
                Rule::unique("users_gimnasios", "usuario")->where("gimnasio", $this->gimnasio->id)
            ]
        ];
    }

    public function messages() : array
    {
        return [
            "email.required" => __("validation.gimnasio.email.required"),
            "email.email" => __("validation.gimnasio.email.email"),
            "email.exists" => __("validation.gimnasio.email.exists"),
            "email.unique" => __("validation.gimnasio.email.unique")
        ];
    }
}

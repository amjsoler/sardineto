<?php

namespace App\Http\Requests;

use App\Rules\ContrasenaActualCorrectaRule;
use Illuminate\Foundation\Http\FormRequest;

class CambiarContrasenaFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "contrasenaActual" => ["required", new ContrasenaActualCorrectaRule()],
            "nuevaContrasena" => "required|confirmed"
        ];
    }

    public function messages()
    {
        return [
            "contrasenaActual.required" => __("validation.usuario.contrasenaActual.required"),
            "nuevaContrasena.required" => __("validation.usuario.nuevaContrasena.required"),
            "nuevaContrasena.confirmed" => __("validation.usuario.nuevaContrasena.confirmed"),
        ];
    }
}

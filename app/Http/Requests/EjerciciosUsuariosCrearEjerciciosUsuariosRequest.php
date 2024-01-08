<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EjerciciosUsuariosCrearEjerciciosUsuariosRequest extends FormRequest
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
            "unorm" => "required|decimal:0,2"
        ];
    }

    public function messages()
    {
        return [
            "unorm.required" => __("validation.ejerciciousuario.unorm.required"),
            "unorm.decimal" => __("validation.ejerciciousuario.unorm.decimal")
        ];
    }
}

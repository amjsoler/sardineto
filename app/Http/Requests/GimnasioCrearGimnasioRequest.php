<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GimnasioCrearGimnasioRequest extends FormRequest
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
            "nombre" => "required|max:150",
            "descripcion" => "nullable|max:5000",
            "logo" => "nullable",
            "direccion" => "nullable|max:200"
        ];
    }

    public function messages(): array
    {
        return  [
            "nombre.required" => __("validation.gimnasio.nombre.required"),
            "nombre.max" => __("validation.gimnasio.nombre.max"),
            "descripcion.max" => __("validation.gimnasio.descripcion.max"),
            "direccion.max" => __("validation.gimnasio.direccion.max")
        ];
    }
}

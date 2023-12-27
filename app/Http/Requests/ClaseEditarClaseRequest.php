<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaseEditarClaseRequest extends FormRequest
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
            "nombre" => "max:150",
            "descripcion" => "max:5000",
            "plazas" => "integer|min:1",
        ];
    }

    public function messages()
    {
        return [
            "nombre.max" => __("validation.clase.nombre.max"),
            "descripcion.max" => __("validation.clase.descripcion.max"),
            "plazas.integer" => __("validation.clase.plazas.integer"),
            "plazas.min" => __("validation.clase.plazas.min"),
        ];
    }
}

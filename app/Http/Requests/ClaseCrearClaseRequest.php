<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiClasePerteneceAGimnasio;
use Illuminate\Foundation\Http\FormRequest;

class ClaseCrearClaseRequest extends FormRequest
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
            "fechayhora" => "required",
            "plazas" => "required|integer|min:1",
        ];
    }

    public function messages()
    {
        return [
            "nombre.required" => __("validation.clase.nombre.required"),
            "nombre.max" => __("validation.clase.nombre.max"),
            "descripcion.max" => __("validation.clase.descripcion.max"),
            "fechayhora.required" => __("validation.clase.fechayhora.required"),
            "plazas.required" => __("validation.clase.plazas.required"),
            "plazas.integer" => __("validation.clase.plazas.integer"),
            "plazas.min" => __("validation.clase.plazas.min"),
        ];
    }
}

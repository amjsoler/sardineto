<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaseCrearClaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nombre" => "required|max:150",
            "descripcion" => "nullable|max:5000",
            "fechayhora" => "required|date",
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
            "fechayhora.date" => __("validation.clase.fechayhora.date"),
            "plazas.required" => __("validation.clase.plazas.required"),
            "plazas.integer" => __("validation.clase.plazas.integer"),
            "plazas.min" => __("validation.clase.plazas.min"),
        ];
    }
}

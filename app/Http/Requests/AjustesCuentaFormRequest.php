<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjustesCuentaFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "alertasporcorreo" => "required|boolean",
            "alertaspornotificacion" => "required|boolean"
        ];
    }

    public function messages()
    {
        return [
            "alertasporcorreo.required" => __("validation.usuario.alertasporcorreo.required"),
            "alertasporcorreo.boolean" => __("validation.usuario.alertasporcorreo.boolean"),
            "alertaspornotificacion.required" => __("validation.usuario.alertaspornotificacion.required"),
            "alertaspornotificacion.boolean" => __("validation.usuario.alertaspornotificacion.boolean"),
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class EnviarSugerenciaFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "texto" => "required|string|max:500"
        ];
    }

    public function messages()
    {
        return [
            "texto.required" => __("validation.enviarsugerencia.texto.required"),
            "texto.string" => __("validation.enviarsugerencia.texto.string"),
            "texto.max" => __("validation.enviarsugerencia.texto.max"),
        ];
    }
}

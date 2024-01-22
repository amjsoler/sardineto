<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClaseDesapuntarseDeClaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(
            [
                "claseId" => $this->clase->id,
                "fechayhora" => $this->clase->fechayhora
            ]
        );
    }

    public function rules(): array
    {
        return [
            "claseId" => [
                Rule::exists("usuarios_participan_clases", "clase")->where("usuario", auth()->user()->id)
            ],
            "fechayhora" => "after:now"
        ];
    }

    public function messages()
    {
        return [
            "claseId.exists" => __("validation.usuarioApuntaClase.usuarioId.exists"),
            "fechayhora.after" => __("validation.usuarioApuntaClase.fechayhora.after"),
        ];
    }
}

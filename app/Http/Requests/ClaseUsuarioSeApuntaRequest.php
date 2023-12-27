<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClaseUsuarioSeApuntaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge(
            [
                "claseId" => $this->clase->id,
                "usuarioId" => auth()->user()->id
            ]
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "usuarioId" => [
                Rule::exists("usuarios_gimnasios", "usuario")->where("invitacion_aceptada", true),
                Rule::unique("usuarios_participan_clases", "usuario")->where("clase", $this->claseId)
            ],
        ];
    }

    public function messages() {
        return [
            "usuarioId.exists" => __("validation.usuarioApuntaClase.exists"),
            "usuarioId.unique" => __("validation.usuarioApuntaClase.unique"),
        ];
    }
}

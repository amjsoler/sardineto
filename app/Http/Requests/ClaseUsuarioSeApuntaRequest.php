<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiQuedanPlazasEnLaClase;
use App\Rules\ComprobarSiUserReuneRequisitosParaApuntarseAClase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClaseUsuarioSeApuntaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge(
            [
                "gimnasioId" => $this->gimnasio->id,
                "claseId" => $this->clase->id,
                "usuarioId" => auth()->user()->id
            ]
        );
    }

    public function rules(): array
    {
        return [
            "usuarioId" => [
                Rule::unique("usuarios_participan_clases", "usuario")->where("clase", $this->claseId),
                new ComprobarSiUserReuneRequisitosParaApuntarseAClase($this->gimnasioId)
            ],
            "claseId" => [
                new ComprobarSiQuedanPlazasEnLaClase()
            ]
        ];
    }

    public function messages() {
        return [
            "usuarioId.unique" => __("validation.usuarioApuntaClase.unique"),
        ];
    }
}

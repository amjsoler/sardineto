<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiUsuarioYaHaAceptadoLaInvitacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GimnasioReenviarInvitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge(["usuarioId" => $this->usuario->id, "gimnasioId" => $this->gimnasio->id]);
    }

    public function rules(): array
    {
        return [
            "usuarioId" => [
                Rule::exists("usuarios_gimnasios", "usuario")->where("gimnasio", $this->gimnasio->id),
                new ComprobarSiUsuarioYaHaAceptadoLaInvitacion($this->gimnasio, $this->usuario)
            ]

        ];
    }

    public function messages()
    {
        return [
            "usuarioId.exists" => __("validation.gimnasio.usuarioId.exists"),
        ];
    }
}

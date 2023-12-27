<?php

namespace App\Http\Requests;

use App\Rules\ComprobarSiUsuarioYaEstaInvitadoAGimnasio;
use App\Rules\ComprobarSiUsuarioYaHaAceptadoLaInvitacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GimnasioReenviarInvitacionRequest extends FormRequest
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
        $this->merge(["usuarioId" => $this->usuario->id, "gimnasioId" => $this->gimnasio->id]);
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

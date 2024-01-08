<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GimnasioAnyadirAdministradorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(["gimnasioId" => $this->gimnasio->id, "usuarioId" => $this->usuario->id]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "gimnasioId" => Rule::unique("administradores", "gimnasio")->where("usuario", $this->usuarioId)
        ];
    }

    public function messages()
    {
        return [
            "gimnasioId.unique" => __("validation.gimnasio.gimnasioId.unique")
        ];
    }
}

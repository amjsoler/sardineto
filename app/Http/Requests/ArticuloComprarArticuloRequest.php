<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticuloComprarArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(["stockdisponible" => $this->articulo->stock]);
    }

    public function rules(): array
    {
        return [
            "stockdisponible" => "integer|min:1"
        ];
    }

    public function messages()
    {
        return [
            "stockdisponible.min" => __("validation.articulo.stockdisponible.min")
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetricaCrearmetricaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "peso" => "required|decimal:0,2",
            "porcentaje_graso" => "required|decimal:0,2"
        ];
    }

    public function messages()
    {
        return [
            "peso.required" => __("validation.metrica.peso.required"),
            "peso.decimal" => __("validation.metrica.peso.decimal"),
            "porcentaje_graso.required" => __("validation.metrica.porcentaje_graso.required"),
            "porcentaje_graso.decimal" => __("validation.metrica.porcentaje_graso.decimal")
        ];
    }
}

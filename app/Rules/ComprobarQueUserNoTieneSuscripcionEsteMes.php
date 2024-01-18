<?php

namespace App\Rules;

use App\Models\Gimnasio;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarQueUserNoTieneSuscripcionEsteMes implements ValidationRule
{
    public $gimnasio;

    public function __construct($gimnasio){$this->gimnasio = $gimnasio;}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(Gimnasio::find($this->gimnasio)
                ->suscripciones()
                ->where("usuario", $value)
                ->whereYear("created_at", now()->year)
                ->whereMonth("created_at", now()->month)
                ->exists())
        {
            $fail(__("validation.suscripcion.usuarioQueSeSuscribe.ComprobarQueUserNoTieneSuscripcionEsteMes"));
        }
    }
}

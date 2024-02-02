<?php

namespace App\Rules;

use App\Models\Gimnasio;
use App\Models\Tarifa;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarQueUserNoTieneSuscripcionEsteMes implements ValidationRule
{
    public $gimnasio;
    public $tarifa;

    public function __construct($gimnasio, $tarifa){$this->gimnasio = $gimnasio; $this->tarifa = $tarifa;}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(Tarifa::find($this->tarifa)->tipo === "suscripcion"){
            if(Gimnasio::find($this->gimnasio)
                ->suscripciones()
                ->with("tarifaALaQuePertenece", function($q){return $q->where("tipo", "suscripcion");})
                ->where("usuario", $value)
                ->whereYear("created_at", now()->year)
                ->whereMonth("created_at", now()->month)
                ->exists())
            {
                $fail(__("validation.suscripcion.usuarioQueSeSuscribe.ComprobarQueUserNoTieneSuscripcionEsteMes"));
            }
        }
    }
}

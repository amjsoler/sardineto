<?php

namespace App\Rules;

use App\Models\Gimnasio;
use App\Models\Tarifa;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use function PHPUnit\Framework\isEmpty;

class ComprobarQueUserNoTieneSuscripcionEsteMes implements ValidationRule
{
    public $gimnasio;
    public $tarifa;

    public function __construct($gimnasio, $tarifa){$this->gimnasio = $gimnasio; $this->tarifa = $tarifa;}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tarifa = Tarifa::find($this->tarifa);
        if(!empty($tarifa) && $tarifa->tipo === "suscripcion"){
            if(Gimnasio::find($this->gimnasio)
                ->suscripciones()
                ->whereHas("tarifaALaQuePertenece", function($q){$q->where("tipo","suscripcion");})
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

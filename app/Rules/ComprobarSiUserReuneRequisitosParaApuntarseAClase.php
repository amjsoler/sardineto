<?php

namespace App\Rules;

use App\Models\Gimnasio;
use App\Models\Suscripcion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiUserReuneRequisitosParaApuntarseAClase implements ValidationRule
{
    public $gimnasioId;

    public function __construct($gimnasioId)
    {
        $this->gimnasioId = $gimnasioId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //Compruebo si tiene suscripción activa este mes
        $suscripcionActiva = Suscripcion::with("tarifaALaQuePertenece")
            ->where("usuario", $value)
            ->where("gimnasio", $this->gimnasioId)
            ->whereMonth("created_at", "=", now()->month)
            ->where("pagada", "!=", null)
            ->first();
//TODO
        if($suscripcionActiva === null){
            $fail(__("validation.clase.usuarioId.ComprobarSiUserReuneRequisitosParaApuntarseAClase"));
        }else{
            //Compruebo si este mes se ha apuntado al total de créditos que otorga la tarifa de la suscripción
            $creditosUsados = auth()->user()->clasesEnLasQueParticipa()
                ->where("gimnasio", $this->gimnasioId)
                ->wherePivot("created_at", now()->month)
                ->count();

            if($creditosUsados >= $suscripcionActiva->TarifaALaQuePertenece->creditos)
            {
                $fail(__("validation.clase.usuarioId.ComprobarSiUserReuneRequisitosParaApuntarseAClase"));
            }
        }

    }
}

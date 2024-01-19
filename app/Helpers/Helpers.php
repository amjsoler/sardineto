<?php

namespace App\Helpers;

use App\Enums\TiposTarifa;
use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\User;

class Helpers
{
    public static function dameSuscripcionActivaOAbonoDeUsuario(User $usuario, Gimnasio $gimnasio) : Suscripcion|null
    {
        //Siempre se priorizará el uso de las suscripciones por encima de los abonos en caso de que ambos estén disponibles

        $suscripcion = Suscripcion::where("usuario", $usuario->id)
            ->where("gimnasio", $gimnasio->id)
            ->where("pagada", "!=", null)
            ->where("creditos_restantes", ">", "0")
            ->whereMonth("created_at", now()->month)
            ->whereHas("tarifaALaQuePertenece", function($q){
                $q->where("tipo", TiposTarifa::SUSCRIPCION);
            })
            ->first();

        if(isset($suscripcion)){
            return $suscripcion;
        }

        //Si no se ha devuelto una suscripción es porque no hay ninguna activa. Compruebo ahora los abonos
        $abono = Suscripcion::where("usuario", $usuario->id)
            ->where("gimnasio", $gimnasio->id)
            ->where("pagada", "!=", null)
            ->where("creditos_restantes", ">", "0")
            ->whereHas("tarifaALaQuePertenece", function($q){
                $q->where("tipo", TiposTarifa::ABONO);
            })
            ->first();

        if(isset($abono)){
            return $abono;
        }

        return null;
    }
}

<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Gimnasio;
use App\Models\Tarifa;
use App\Models\User;

class TarifaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function verTarifas(User $usuario, Gimnasio $gimnasio): bool
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearTarifas(User $usuario, Gimnasio $gimnasio): bool
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function editarTarifas(User $usuario, Gimnasio $gimnasio, Tarifa $tarifa)
    {
        $puede = true;

        if(!PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)){
            $puede = false;
        }

        if($puede &&
            $gimnasio->id !== $tarifa->gimnasio
        ){
            $puede = false;
        }

        return $puede;
    }

    public function eliminarTarifas(User $usuario, Gimnasio $gimnasio, Tarifa $tarifa)
    {
        $puede = true;

        if(!PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)){
            $puede = false;
        }

        if($puede &&
            $gimnasio->id !== $tarifa->gimnasio
        ){
            $puede = false;
        }

        return $puede;
    }
}

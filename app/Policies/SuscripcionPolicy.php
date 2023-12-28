<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\User;

class SuscripcionPolicy
{
    public function verSuscripciones(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearSuscripciones(User $usuario, Gimnasio $gimnasio)
    {
        return $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuario->id)->count() === 1;
    }

    public function crearSuscripcionesComoAdmin(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function editarSuscripciones(user $usuario, Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        return $gimnasio->id === $suscripcion->gimnasio &&
            (PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) ||
            $suscripcion->usuario === $usuario->id);
    }

    public function eliminarSuscripciones(user $usuario, Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) &&
            $gimnasio->id === $suscripcion->gimnasio;
    }

    public function marcarSuscripcionesPagadas(user $usuario, Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        return $gimnasio->id === $suscripcion->gimnasio &&
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }


}

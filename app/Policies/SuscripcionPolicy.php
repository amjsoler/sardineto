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
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearSuscripciones(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio);
    }

    public function crearSuscripcionesComoAdmin(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function editarSuscripciones(user $usuario, Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        return $this->comprobarSiSuscripcionPerteneceAGimnasio($suscripcion, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio)
            );
    }

    public function eliminarSuscripciones(user $usuario, Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        return $this->comprobarSiSuscripcionPerteneceAGimnasio($suscripcion, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio)
            );
    }

    public function marcarSuscripcionesPagadas(user $usuario, Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        return $this->comprobarSiSuscripcionPerteneceAGimnasio($suscripcion, $gimnasio) &&
        (
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio)
        );
    }

    private function comprobarSiSuscripcionPerteneceAGimnasio(Suscripcion $suscripcion, Gimnasio $gimnasio)
    {
        return $suscripcion->gimnasio === $gimnasio->id;
    }
}

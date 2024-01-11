<?php

namespace App\Helpers;

use App\Models\Gimnasio;
use App\Models\User;

class PolicyHelpers {
    public static function comprobarSiUserEsPropietarioDelGimnasio(
        User $usuario,
        Gimnasio $gimnasio
    )
    {
        return $usuario->id === $gimnasio->propietario;
    }

    public static function comprobarSiUserEstaInvitadoAlGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return $gimnasio->usuariosInvitados()
            ->wherePivot("usuario", $usuario->id)
            ->wherePivot("invitacion_aceptada", true)
            ->exists();
    }

    public static function comprobarSiUserEsAdministradorDelGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return $gimnasio->administradores()->wherePivot("usuario", $usuario->id)->exists();
    }
}

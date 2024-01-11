<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Gimnasio;
use App\Models\User;

class GimnasioPolicy
{
    public function editarGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function eliminarGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function invitarUsuarios(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function reenviarInvitaciones(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearAdministradores(User $user, Gimnasio $gimnasio, User $usuario)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($user, $gimnasio);
    }

    public function quitarAdministradores(User $user, Gimnasio $gimnasio, User $usuario)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($user, $gimnasio);
    }
}

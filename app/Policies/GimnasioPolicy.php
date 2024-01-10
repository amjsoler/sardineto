<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Gimnasio;
use App\Models\User;

class GimnasioPolicy
{
    public function editarGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio);
    }

    public function eliminarGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function invitarUsuarios(User $usuario, Gimnasio $gimnasio)
    {
        return $this->comprobarAdministradorGimnasio($usuario, $gimnasio);
    }
    private function comprobarAdministradorGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return $usuario->id === $gimnasio->propietario;
    }

    public function reenviarInvitaciones(User $usuario, Gimnasio $gimnasio)
    {
        return $this->comprobarAdministradorGimnasio($usuario, $gimnasio);
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

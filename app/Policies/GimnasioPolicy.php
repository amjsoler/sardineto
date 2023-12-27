<?php

namespace App\Policies;

use App\Models\Gimnasio;
use App\Models\User;

class GimnasioPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function editarGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return $this->comprobarAdministradorGimnasio($usuario, $gimnasio);
    }

    public function eliminarGimnasio(User $user, Gimnasio $gimnasio)
    {
        return $this->comprobarAdministradorGimnasio($user, $gimnasio);
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
}

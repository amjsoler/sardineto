<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Clase;
use App\Models\Gimnasio;
use App\Models\User;

class ClasePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {}

    public function verClases(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearClases(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function editarClases(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        return $this->clasePerteneceAGimnasio($clase, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function eliminarClases(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        return $this->clasePerteneceAGimnasio($clase, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function usuarioSePuedeApuntar(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        return $this->clasePerteneceAGimnasio($clase, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );

    }

    public function usuarioSePuedeDesapuntar(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        return $this->clasePerteneceAGimnasio($clase, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    private function clasePerteneceAGimnasio(Clase $clase, Gimnasio $gimnasio)
    {
        return $clase->gimnasio === $gimnasio->id;
    }
}

<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Gimnasio;
use App\Models\Tarifa;
use App\Models\User;

class TarifaPolicy
{
    public function verTarifas(User $usuario, Gimnasio $gimnasio): bool
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearTarifas(User $usuario, Gimnasio $gimnasio): bool
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function editarTarifas(User $usuario, Gimnasio $gimnasio, Tarifa $tarifa)
    {
        return $this->comprobarSiTarifaPerteneceAGimnasio($tarifa, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function eliminarTarifas(User $usuario, Gimnasio $gimnasio, Tarifa $tarifa)
    {
        return $this->comprobarSiTarifaPerteneceAGimnasio($tarifa, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    private function comprobarSiTarifaPerteneceAGimnasio(Tarifa $tarifa, Gimnasio $gimnasio)
    {
        return $tarifa->gimnasio === $gimnasio->id;
    }
}

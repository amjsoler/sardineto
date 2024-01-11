<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Ejercicio;
use App\Models\EjercicioUsuario;
use App\Models\Gimnasio;
use App\Models\User;

class EjercicioUsuarioPolicy
{
    public function verRegistrosDePeso(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function verRegistrosDePesoPorEjercicio(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return $this->comprobarSiEjercicioPerteneceAGimnasio($ejercicio, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function crearRegistrosDePeso(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return $this->comprobarSiEjercicioPerteneceAGimnasio($ejercicio, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function eliminarRegistrosDePeso(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio, EjercicioUsuario $ejercicioUsuario)
    {
        return $this->comprobarSiEjercicioPerteneceAGimnasio($ejercicio, $gimnasio) &&
            $ejercicioUsuario->usuario === $usuario->id &&
            $ejercicioUsuario->ejercicio === $ejercicio->id &&
            (
                PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    private function comprobarSiEjercicioPerteneceAGimnasio(Ejercicio $ejercicio, Gimnasio $gimnasio)
    {
        return $ejercicio->gimnasio === $gimnasio->id;
    }
}

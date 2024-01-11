<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Clase;
use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;

class EjercicioPolicy
{
    public function verEjercicios(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearEjercicios(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function modificarEjercicios(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return $this->comprobarSiEjercicioPerteneceAGimnasio($ejercicio, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function eliminarEjercicios(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return $this->comprobarSiEjercicioPerteneceAGimnasio($ejercicio, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function asociarEjerciciosAClase(user $usuario, Gimnasio $gimnasio, Clase $clase, Ejercicio $ejercicio)
    {
        return $this->comprobarSiEjercicioPerteneceAGimnasio($ejercicio, $gimnasio) &&
            $clase->gimnasio === $gimnasio->id &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function desasociarEjerciciosAClase(user $usuario, Gimnasio $gimnasio, Clase $clase, Ejercicio $ejercicio)
    {
        return $this->comprobarSiEjercicioPerteneceAGimnasio($ejercicio, $gimnasio) &&
            $clase->gimnasio === $gimnasio->id &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    private function comprobarSiEjercicioPerteneceAGimnasio(Ejercicio $ejercicio, Gimnasio $gimnasio)
    {
        return $ejercicio->gimnasio === $gimnasio->id;
    }
}

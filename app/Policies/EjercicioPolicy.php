<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EjercicioPolicy
{
    public function verEjercicios(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) ||
                $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuario->id)->exists();
    }

    public function crearEjercicios(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function modificarEjercicios(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) &&
            $ejercicio->gimnasio === $gimnasio->id;
    }

    public function eliminarEjercicios(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) &&
            $ejercicio->gimnasio === $gimnasio->id;
    }
}

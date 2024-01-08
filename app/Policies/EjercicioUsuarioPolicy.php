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
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio);
    }

    public function verRegistrosDePesoPorEjercicio(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) &&
            $gimnasio->id === $ejercicio->gimnasio;
    }

    public function crearRegistrosDePeso(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) &&
            $gimnasio->id === $ejercicio->gimnasio;
    }

    public function eliminarRegistrosDePeso(User $usuario, Gimnasio $gimnasio, Ejercicio $ejercicio, EjercicioUsuario $ejercicioUsuario)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) &&
            $gimnasio->id === $ejercicio->gimnasio &&
            $ejercicioUsuario->ejercicio === $ejercicio->id &&
            ($ejercicioUsuario->usuario === $usuario->id);
    }
}

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
}

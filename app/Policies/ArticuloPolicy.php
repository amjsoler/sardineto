<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;

class ArticuloPolicy
{
    public function verArticulos(User $usuario, Gimnasio $gimnasio)
    {
        return $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuario->id)->count() === 1 ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearArticulos(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function editarArticulos(User $usuario, Gimnasio $gimnasio, Articulo $articulo)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) &&
            $gimnasio->id === $articulo->gimnasio;
    }

    public function eliminarArticulos(User $usuario, Gimnasio $gimnasio, Articulo $articulo)
    {
        return PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio) &&
            $gimnasio->id === $articulo->gimnasio;
    }
}

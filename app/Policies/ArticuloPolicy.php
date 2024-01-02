<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Http\Requests\ArticuloPagarCompraRequest;
use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use App\Models\UsuarioCompraArticulo;

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

    public function verMiHistorialDeCompras(User $user, Gimnasio $gimnasio)
    {
        return $gimnasio->usuariosInvitados()->wherePivot("usuario", $user->id)->count() === 1;
    }

    public function comprarArticulos(User $usuario, Gimnasio $gimnasio, Articulo $articulo)
    {
        return $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuario->id)->exists()
            && $gimnasio->articulos()->whereIn("id", [$articulo->id])->exists();
    }

    public function pagarCompras(User $usuario, Gimnasio $gimnasio, UsuarioCompraArticulo $compra)
    {
        return $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuario->id)->exists() &&
            $compra->gimnasio === $gimnasio->id &&
            $compra->usuario === $usuario->id;
    }
}

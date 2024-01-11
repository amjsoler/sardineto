<?php

namespace App\Policies;

use App\Helpers\PolicyHelpers;
use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use App\Models\UsuarioCompraArticulo;

class ArticuloPolicy
{
    public function verArticulos(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function crearArticulos(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function editarArticulos(User $usuario, Gimnasio $gimnasio, Articulo $articulo)
    {
        return $this->comprobarSiArticuloPerteneceAGimnasio($articulo, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function eliminarArticulos(User $usuario, Gimnasio $gimnasio, Articulo $articulo)
    {
        return $this->comprobarSiArticuloPerteneceAGimnasio($articulo, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function verMiHistorialDeCompras(User $usuario, Gimnasio $gimnasio)
    {
        return PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
            PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio);
    }

    public function comprarArticulos(User $usuario, Gimnasio $gimnasio, Articulo $articulo)
    {
        return $this->comprobarSiArticuloPerteneceAGimnasio($articulo, $gimnasio) &&
            (
                PolicyHelpers::comprobarSiUserEstaInvitadoAlGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    public function pagarCompras(User $usuario, Gimnasio $gimnasio, UsuarioCompraArticulo $compra)
    {
        return $compra->gimnasio === $gimnasio->id &&
            (
                PolicyHelpers::comprobarSiUserEsAdministradorDelGimnasio($usuario, $gimnasio) ||
                PolicyHelpers::comprobarSiUserEsPropietarioDelGimnasio($usuario, $gimnasio)
            );
    }

    private function comprobarSiArticuloPerteneceAGimnasio(Articulo $articulo, Gimnasio $gimnasio)
    {
        return $articulo->gimnasio === $gimnasio->id;
    }
}

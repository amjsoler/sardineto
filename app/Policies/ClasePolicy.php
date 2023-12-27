<?php

namespace App\Policies;

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
        //Compruebo si el usuario está invitado en el gimnasio
        if($usuario->gimnasiosInvitado()
            ->wherePivot("gimnasio", $gimnasio->id)
            ->wherePivot("invitacion_aceptada", true)
            ->count() > 0 ||
        $usuario->id === $gimnasio->propietario)
        {
            return true;
        }
        else{
            return false;
        }
    }

    public function crearClases(User $usuario, Gimnasio $gimnasio)
    {
        return $this->userEsAdminDelGimnasio($usuario, $gimnasio);
    }

    public function editarClases(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        $auth = true;

        if(!$this->clasePerteneceAGimnasio($clase, $gimnasio)){
            $auth = false;
        }

        if(!$this->userEsAdminDelGimnasio($usuario, $gimnasio)){
            $auth = false;
        }

        return $auth;
    }

    public function eliminarClases(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        $auth = true;

        if(!$this->clasePerteneceAGimnasio($clase, $gimnasio)){
            $auth = false;
        }

        if(!$this->userEsAdminDelGimnasio($usuario, $gimnasio)){
            $auth = false;
        }

        return $auth;
    }

    public function usuarioSePuedeApuntar(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        $auth = true;

        if(!$this->clasePerteneceAGimnasio($clase, $gimnasio)){
            $auth = false;
        }

        return $auth;
    }

    public function usuarioSePuedeDesapuntar(User $usuario, Gimnasio $gimnasio, Clase $clase)
    {
        $auth = true;

        if(!$this->clasePerteneceAGimnasio($clase, $gimnasio)){
            $auth = false;
        }

        return $auth;
    }

    private function clasePerteneceAGimnasio(Clase $clase, Gimnasio $gimnasio)
    {
        return $clase->gimnasio === $gimnasio->id;
    }

    private function userEsAdminDelGimnasio(User $usuario, Gimnasio $gimnasio)
    {
        return $usuario->id === $gimnasio->propietario;
    }
}

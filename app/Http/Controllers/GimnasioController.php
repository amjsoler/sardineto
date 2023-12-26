<?php

namespace App\Http\Controllers;

use App\Http\Requests\GimnasioCrearGimnasioRequest;
use App\Http\Requests\GimnasioEditarGimnasioRequest;
use App\Http\Requests\GimnasioInvitarUsuarioRequest;
use App\Models\Gimnasio;
use App\Models\User;

class GimnasioController extends Controller
{
    public function misGimnasios()
    {
        //TODO Devolver también los gimnasios que sigues
        return response()->json(auth()->user()->gimnasiosPropietario, 200);
    }

    public function crearGimnasio(GimnasioCrearGimnasioRequest $request)
    {
        $gimnasio = new Gimnasio($request->all());
        auth()->user()->gimnasiosPropietario()->save($gimnasio);

        return response()->json($gimnasio->refresh(), 200);
    }

    public function editarGimnasio(Gimnasio $gimnasio, GimnasioEditarGimnasioRequest $request)
    {
        $gimnasio->update($request->all());

        return response()->json($gimnasio,200);
    }

    public function eliminarGimnasio(Gimnasio $gimnasio)
    {
        $gimnasio->delete();

        return response()->json(null, 200);
    }

    public function invitarUsuario(Gimnasio $gimnasio, GimnasioInvitarUsuarioRequest $request)
    {
        $usuario = User::where("email", $request->get("email"))->first();

        $gimnasio->usuariosInvitados()->attach($usuario);
    }
}

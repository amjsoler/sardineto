<?php

namespace App\Http\Controllers;

use App\Events\UsuarioInvitadoAGimnasio;
use App\Http\Requests\GimnasioAnyadirAdministradorRequest;
use App\Http\Requests\GimnasioCrearGimnasioRequest;
use App\Http\Requests\GimnasioEditarGimnasioRequest;
use App\Http\Requests\GimnasioInvitarUsuarioRequest;
use App\Http\Requests\GimnasioReenviarInvitacionRequest;
use App\Models\Gimnasio;
use App\Models\User;
use App\Notifications\CorreoConfirmacionUsuarioInvitadoAGimnasio;
use Exception;
use Illuminate\Support\Facades\Notification;

class GimnasioController extends Controller
{
    public function misGimnasios()
    {
        $gimnasiosPropietario = auth()->user()->gimnasiosPropietario;
        $gimnasiosInvitado = auth()->user()->gimnasiosInvitado()->wherePivot("invitacion_aceptada", true)->get();

        $coleccionFinal = $gimnasiosInvitado->merge($gimnasiosPropietario);

        return response()->json($coleccionFinal->sortBy("nombre"), 200);
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

        UsuarioInvitadoAGimnasio::dispatch($usuario, $gimnasio, $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuario->id)->withPivot("token_aceptacion")->first()->pivot->token_aceptacion);

        return response()->json();
    }

    public function aceptarInvitacion(Gimnasio $gimnasio, string $hash)
    {
        $response["code"] = "";
        $response["message"] = "";

        try{
            $usuarioGimnasioId = $gimnasio->usuariosInvitados()
                ->wherePivot("token_aceptacion", $hash)
                ->first()->id;

            $gimnasio->usuariosInvitados()
                ->updateExistingPivot($usuarioGimnasioId, [
                "invitacion_aceptada" => true
            ]);

            $response["code"] = 0;

            return view("gimnasio.invitacionAceptada", compact("response"));
        }
        catch (Exception $e){
            $response["code"] = -2;

            return view("gimnasio.invitacionAceptada", compact("response"));
        }
    }

    public function reenviarInvitacion(Gimnasio $gimnasio, User $usuario, GimnasioReenviarInvitacionRequest $request)
    {
        $token = $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuario->id)
            ->withPivot("token_aceptacion")
            ->first()->pivot->token_aceptacion;
        Notification::send($usuario, new CorreoConfirmacionUsuarioInvitadoAGimnasio($usuario, $gimnasio, $token));

        return response()->json();
    }

    public function anyadirAdministrador(Gimnasio $gimnasio, User $usuario, GimnasioAnyadirAdministradorRequest $request)
    {
        $gimnasio->administradores()->attach($usuario->id);

        return response()->json();
    }

    public function quitarAdministrador(Gimnasio $gimnasio, User $usuario)
    {
        $gimnasio->administradores()->detach($usuario->id);

        return response()->json();
    }
}

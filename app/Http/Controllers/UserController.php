<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjustesCuentaFormRequest;
use App\Http\Requests\EnviarSugerenciaFormRequest;
use App\Models\User;
use App\Notifications\EnviarSugerenciaAlAdministrador;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function enviarSugerencia(EnviarSugerenciaFormRequest $request)
    {
        Notification::send(
            User::where("email", env("ADMIN_AUTORIZADO"))->first(),
            new EnviarSugerenciaAlAdministrador($request->get("texto"), auth()->user()->email));

        return response()->json();
    }

    public function eliminarCuenta()
    {
        auth()->user()->delete();

        return response()->json();
    }

    public function guardarAjustesCuentaUsuario(AjustesCuentaFormRequest $request)
    {
       auth()->user()->update($request->all());

        return response()->json(auth()->user());
    }
}

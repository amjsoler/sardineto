<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambiarContrasenaFormRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RecuperarCuentaFormRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\AccountVerifyToken;
use App\Models\RecuperarCuentaToken;
use App\Models\User;
use App\Notifications\RecuperarCuenta;
use App\Notifications\VerificarNuevaCuentaUsuario;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthentication extends Controller
{
    public function login(LoginRequest $request)
    {
        $response = array();

        if (Auth::attempt($request->only("email", "password"))) {
            $token = auth()->user()->createToken("authToken")->plainTextToken;

            $finalData = [...auth()->user()->toArray(), ...["access_token" => $token, "token_type" => "Bearer"]];
            $response["status"] = 200;
            $response["data"] = $finalData;
        }else{
            $response["status"] = 462;
            $response["data"] = "La contraseña no es correcta";
        }

        return response()->json(
            $response["data"],
            $response["status"]
        );
    }

    public function register(RegisterRequest $request)
    {
        //Creo el usuario
        $nuevoUsuario = User::make([
            "name" => $request->get("name"),
            "password" =>$request->get("password")
        ]);
        $nuevoUsuario->email = $request->get("email");
        $nuevoUsuario->save();

        //Inicio sesión para disponer del id en el auth()
        Auth::attempt($request->only("email", "password"));

        //Creo el token de usuario
        $token = $nuevoUsuario->createToken("authToken")->plainTextToken;
        $nuevoUsuario->access_token = $token;
        $nuevoUsuario->token_type = "Bearer";

        //Mandando notificación con el enlace de verificación de la cuenta
        $this->mandarCorreoVerificacionCuenta();

        return response()->json($nuevoUsuario);
    }

    public function recuperarCuenta(RecuperarCuentaFormRequest $request)
    {
        $usuario = User::where("email", $request->get("correo"))->first();

        if(isset($usuario)){
            //Creo el nuevo token
            $validez = now()->addMinute(env("TIEMPO_VALIDEZ_TOKEN_RECUPERAR_CUENTA_EN_MINUTOS"));

            //Primero vacío los tokens del usuario para crear uno nuevo puesto un usuario solo puede tener un token
            RecuperarCuentaToken::where("usuario", $usuario->id)->delete();

            //Ahora creo el nuevo token
            $nuevoRecuperarCuenta = RecuperarCuentaToken::create([
                "usuario" => $usuario->id,
                "token" => str_replace("/", "", Hash::make(now())),
                "valido_hasta" => $validez,
            ]);

            //Se ha creado el token correctamente, ahora lo mando por correo
            $usuario->notify(new RecuperarCuenta($nuevoRecuperarCuenta->token));
        }

        return response()->json();
    }

    public function mandarCorreoVerificacionCuenta()
    {
        //Creo el periodo de validez del token
        $validez = now()->addMinute(env("TIEMPO_VALIDEZ_TOKEN_VERIFICACION_EN_MINUTOS"));

        //Primero borro los tokens del usuario, ya que solo puede tener uno
        AccountVerifyToken::where("usuario", auth()->user()->id)->delete();

        //Ahora creo el token
        $nuevoAccountVerify = AccountVerifyToken::create([
            "usuario" => auth()->user()->id,
            "token" => str_replace("/", "", Hash::make(now())),
            "valido_hasta" => $validez
        ]);

        //Se ha creado el token correctamente, ahora lo mando por correo
        auth()->user()->notify(new VerificarNuevaCuentaUsuario($nuevoAccountVerify->token));
    }

    public function cambiarContrasena(CambiarContrasenaFormRequest $request)
    {
        $user = auth()->user();
        $user->password = Hash::make($request->nuevaContrasena);
        $user->save();

        return response()->json();
    }
}

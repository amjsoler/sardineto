<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecuperarCuentaPostRequest;
use App\Models\AccountVerifyToken;
use App\Models\RecuperarCuentaToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authentication extends Controller
{
    public function verificarCuentaConToken(string $token)
    {
        $result = false;

        //Consulto el token y veo si todavía es válido
        $accountVerifyToken = AccountVerifyToken::where("token", "=", $token)
            ->where("valido_hasta", ">", now())
            ->first();

        if(isset($accountVerifyToken)){
            $user = User::find($accountVerifyToken->usuario);
            $user->email_verified_at = now();
            $user->save();

            $result = true;
        }

        return view("cuentaUsuario/verificarCuenta", compact("result"));
    }

    public function recuperarCuentaGet(string $token)
    {
        //Consulto el token y veo si todavía es válido
        $recuperarCuentaToken = RecuperarCuentaToken::where("token", $token)
            ->where("valido_hasta", ">", now())
            ->first();

        $response = array();

        if(isset($recuperarCuentaToken)){
            $response["code"] = 0;
            $response["data"] = $recuperarCuentaToken->token;
        }else{
            $response["code"] = -2;
        }

        return view("cuentaUsuario.recuperarCuenta", compact("response"));
    }

    /**
     * Método para guardar la nueva contraseña de usuario
     *
     * @param RecuperarCuentaPostRequest $request request que contiene la contraseña y el token
     *
     * @return void
     *   0: ok
     * -11: Excepción
     * -12: El token no es válido
     * -13: Fallo al guardar la nueva contraseña
     */
    public function recuperarCuentaPost(RecuperarCuentaPostRequest $request)
    {
        $response = [
            "status" => "",
            "code" => "",
            "statusText" => "",
            "data" => []
        ];

        try{
            //Log de entrada
            Log::debug("Entrando al recuperarCuentaPost de Authentication",
                array(
                    "request: " => $request->all()
                ));

            //Consulto el token y veo si todavía es válido
            $result = RecuperarCuentaToken::consultarToken($request->get("token"));

            $recuperarCuentaResult = $result["data"];

            if($recuperarCuentaResult){
                $resultMarcarVerificacion = User::guardarNuevoPass($recuperarCuentaResult->usuario, $request->get("password"));

                if($resultMarcarVerificacion["code"] == 0){
                    $response["code"] = 0;
                    $response["status"] = 200;
                    $response["statusText"] = "ok";
                }else{
                    $response["code"] = -13;
                    $response["status"] = 400;
                    $response["statusText"] = "ko";
                }
            }else{
                $response["code"] = -12;
                $response["status"] = 400;
                $response["statusText"] = "ko";

                //El token no es válido, no se ha encontrado porque se lo ha inventado cambiando la url o se ha caducado
            }
        }
        catch(Exception $e){
            $response["code"] = -11;
            $response["status"] = 400;
            $response["statusText"] = "ko";

            Log::error($e->getMessage(),
                array(
                    "request: " => $request->all(),
                    "repsonse: " => $response
                )
            );
        }

        //Log de salida
        Log::debug("Saliendo del recuperarCuentaPost de Authentication",
            array(
                "request: " => $request->all(),
                "response: " => $response
            )
        );

        return view("cuentaUsuario.recuperarCuentaResult", compact("response"));
    }
}

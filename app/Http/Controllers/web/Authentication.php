<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecuperarCuentaPostRequest;
use App\Models\AccountVerifyToken;
use App\Models\RecuperarCuentaToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function recuperarCuentaPost(RecuperarCuentaPostRequest $request)
    {
        //Consulto el token y veo si todavía es válido
        $recuperarCuentaToken = RecuperarCuentaToken::where("token", $request->token)->where("valido_hasta", ">", now())->first();

        $response = array();

        if(isset($recuperarCuentaToken)){
            $user = User::find($recuperarCuentaToken->usuario);
            $user->password = Hash::make($request->password);
            $user->save();

            $response["code"] = 0;
        }else{
            $response["code"] = -2;
        }

        return view("cuentaUsuario.recuperarCuentaResult", compact("response"));
    }
}

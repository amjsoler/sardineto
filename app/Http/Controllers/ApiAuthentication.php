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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ApiAuthentication extends Controller
{
    /**
     * Método para loguear a un usuario
     *
     * @param LoginRequest $request Inluye el email y la contraseña
     *
     * @return {access_token, token_type} Si la contraseña no coincide, devuelve un 401
     *   0: OK
     * -11: Excepción
     * -12: No se ha podido iniciar sesión. Quizá haya algún dato incorrecto
     * -13: No se ha podido leer el usuario dado el correo
     */
    public function login(LoginRequest $request)
    {
        $response = [
            "status" => "",
            "code" => "",
            "statusText" => "",
            "data" => []
        ];

        try{
            //Log de entrada
            Log::debug("Entrando al login de ApiAuthentication",
                array(
                    "request: " => $request->all()
                )
            );

            if (Auth::attempt($request->only("email", "password"))) {
                //Si he conseguido iniciar sesión me traigo al user para crear el token
                $userResponse = User::dameUsuarioDadoCorreo($request->get("email"));

                if($userResponse["code"] == 0){
                    $user = $userResponse["data"];
                    $token = $user->createToken("authToken")->plainTextToken;

                    $response["code"] = 0;
                    $response["status"] = 200;
                    $response["data"] = ["access_token" => $token, "token_type" => "Bearer"];
                    $response["statusText"] = "ok";
                }else{
                    $response["code"] = -13;
                    $response["status"] = 401;
                    $response["data"] = "Unauthorized";
                    $response["statusText"] = "Unauthorized";

                    Log::error("Esto no debería fallar, si ya ha conseguido loguearse, la función dameusuarioDadoCorreo debería devolver el usuario",
                        array(
                            "request: " => $request->all()
                        )
                    );
                }
            }else{
                $response["code"] = -12;
                $response["status"] = 401;
                $response["data"] = "Unauthorized";
                $response["statusText"] = "Unauthorized";
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
        Log::debug("Saliendo del login del ApiAuthentication",
            array(
                "request: " => $request->all(),
                "response: " => $response
            )
        );

        return response()->json(
            $response["data"],
            $response["status"]
        );
    }

    public function register(RegisterRequest $request)
    {
        //Creo el usuario
        $nuevoUsuario = User::create([
            "name" => $request->get("name"),
            "email" => $request->get("email"),
            "password" =>$request->get("password")
        ]);

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

    /**
     * Método para inciar el proceso de recuperación de cuenta. Recibe un email y manda un correo a dicho email para cambiar la contraseña
     *
     * @param Request $request Incluye el email para mandar el correo
     *
     * @return \Illuminate\Http\JsonResponse void
     *   0: OK
     * -11: Excepción
     * -12: Fallo al crear el token de recuperación
     */
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
        //Creo el nuevo token
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

    /**
     * Método para que el usuario cambie la contraseña de acceso a la cuenta desde el perfil
     *
     * @param CambiarContrasenaFormRequest $request Contiene la contraseña actual y la nueva junto con su confirmation
     *
     * @return void
     *   0: OK
     * -11: Excepción
     * -12: Error al guardar la nueva contraseña
     */
    public function cambiarContrasena(CambiarContrasenaFormRequest $request)
    {
        $response = [
            "status" => "",
            "code" => "",
            "statusText" => "",
            "data" => []
        ];

        try{
            //Log de entrada
            Log::debug("Entrando al cambiarContrasena de ApiAuthentication",
                array(
                    "userID: " => auth()->user()->id,
                    "request: " => $request
                )
            );

            $resultGuardarNuevaContrasena = User::guardarNuevoPass(auth()->user()->id, $request->get("nuevaContrasena"));

            if($resultGuardarNuevaContrasena["code"] == 0){
                $response["code"] = 0;
                $response["status"] = 200;
                $response["statusText"] = "ok";
            }else{
                $response["code"] = -12;
                $response["status"] = 400;
                $response["statusText"] = "ko";
            }

        }
        catch(Exception $e){
            $response["code"] = -11;
            $response["status"] = 400;
            $response["statusText"] = "ko";

            Log::error($e->getMessage(),
                array(
                    "userID: " => auth()->user()->id,
                    "request: " => $request,
                    "response: " => $response
                )
            );
        }

        //Log de salida
        Log::debug("Saliendo del cambiarContrasena de ApiAuthentication",
            array(
                "userID: " => auth()->user()->id,
                "request: " => $request,
                "response: " => $response
            )
        );

        return $response;
    }
}

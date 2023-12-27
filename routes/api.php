<?php

use App\Http\Controllers\ClaseController;
use App\Http\Controllers\GimnasioController;
use App\Models\Clase;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get("iniciar-sesion", function() {
    $user = User::find(1);
    $token = $user->createToken("authToken")->plainTextToken;

    return response()->json(["token" => $token]);
});



/////////////////////
///// GIMNASIOS /////
/////////////////////

Route::get("gimnasios",
    [GimnasioController::class, "misGimnasios"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->name("mis-gimnasios");

Route::post("gimnasios",
    [GimnasioController::class, "crearGimnasio"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->name("crear-gimnasio");

Route::put("gimnasios/{gimnasio}",
    [GimnasioController::class, "editarGimnasio"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->can("editarGimnasio", "gimnasio")
    ->name("editar-gimnasio");

Route::delete("gimnasios/{gimnasio}",
    [GimnasioController::class, "eliminarGimnasio"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->can("eliminarGimnasio", "gimnasio")
    ->name("eliminar-gimnasio");

Route::post("gimnasios/{gimnasio}/invitar-usuario",
    [GimnasioController::class, "invitarUsuario"])
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("invitarUsuarios", "gimnasio")
    ->name("invitar-usuario");

Route::get("gimnasios/{gimnasio}/reenviar-invitacion/{usuario}",
    [GimnasioController::class, "reenviarInvitacion"])
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("reenviarInvitaciones", "gimnasio")
    ->name("reenviar-invitacion");


//////////////////
///// CLASES /////
//////////////////

Route::get("gimnasios/{gimnasio}/clases",
    [ClaseController::class, "verClases"])
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verClases", [Clase::class, "gimnasio"])
    ->name("ver-clases-de-gimnasio");

Route::post("gimnasios/{gimnasio}/clases",
    [ClaseController::class, "crearClase"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearClases", [Clase::class, "gimnasio"])
    ->name("crear-clase");

Route::put("gimnasios/{gimnasio}/clases/{clase}",
    [ClaseController::class, "editarClase"]
)->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarClases", [Clase::class, "gimnasio", "clase"])
    ->name("editar-clase");

Route::delete("gimnasios/{gimnasio}/clases/{clase}",
    [ClaseController::class, "eliminarClase"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarClases", [Clase::class, "gimnasio", "clase"])
    ->name("eliminar-clase");

Route::get("gimnasios/{gimnasio}/clases/{clase}/apuntarse",
    [ClaseController::class, "usuarioSeApunta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("usuarioSePuedeApuntar", [Clase::class, "gimnasio", "clase"])
    ->name("usuario-se-apunta");

Route::get("gimnasios/{gimnasio}/clases/{clase}/desapuntarse",
    [ClaseController::class, "usuarioSeDesapunta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("usuarioSePuedeDesapuntar", [Clase::class, "gimnasio", "clase"])
    ->name("usuario-se-desapunta");



///////////////////
///// TARIFAS /////
///////////////////

Route::get("gimnasios/{gimnasio}/tarifas", []);
Route::post("gimnasios/{gimnasio}/tarifas", []);
Route::put("gimnasios/{gimnasio}/tarifas/{tarifa}", []);
Route::delete("gimnasios/{gimnasio}/tarifas/{tarifa}", []);



/////////////////////////
///// SUSCRIPCIONES /////
/////////////////////////

Route::get("gimnasios/{gimnasio}/suscripciones", []);
Route::post("gimnasios/{gimnasio}/suscripciones", []);//Como admin
Route::post("gimnasios/{gimnasio}/suscribirse", []);//Como user
Route::put("gimnasios/{gimnasio}/suscripciones/{suscripcion}", []); //Como admin
Route::delete("gimnasios/{gimnasio}/suscripciones/{suscripcion}", []); //Como admin
Route::get("gimnasios/{gimnasio}/suscripciones/{suscripcion}/marcar-pagada"); //Como admin y como tpv


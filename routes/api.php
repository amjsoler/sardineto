<?php

use App\Http\Controllers\GimnasioController;
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

Route::get("gimnasios", [GimnasioController::class, "misGimnasios"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->name("mis-gimnasios");

Route::post("gimnasios", [GimnasioController::class, "crearGimnasio"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->name("crear-gimnasio");

Route::put("gimnasios/{gimnasio}", [GimnasioController::class, "editarGimnasio"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->can("editarGimnasio", "gimnasio")
    ->name("editar-gimnasio");

Route::delete("gimnasios/{gimnasio}", [GimnasioController::class, "eliminarGimnasio"])
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->can("eliminarGimnasio", "gimnasio")
    ->name("eliminar-gimnasio");

Route::post("gimnasios/{gimnasio}/invitar-usuario", [GimnasioController::class, "invitarUsuario"])
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("invitarUsuarios", "gimnasio")
    ->name("invitar-usuario");

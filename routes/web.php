<?php

use App\Http\Controllers\GimnasioController;
use App\Http\Controllers\web\Authentication;
use App\Models\User;
use App\Notifications\PruebaBorrar;
use App\Notifications\PruebaQueuedBorrar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

///////////////////////////////
/////// RUTAS DE CUENTA ///////
///////////////////////////////

Route::get("/", function () {
    return "Tienes una sesión iniciada";
})
    ->middleware("auth:sanctum")
    ->name("consesion");

Route::get("verificar-cuenta/{token}",
    [Authentication::class, "verificarCuentaConToken"]
)
    ->name("verificarcuentacontoken");

Route::get("recuperar-cuenta/{token}",
    [Authentication::class, "recuperarCuentaGet"]
)
    ->name("recuperarcuentaget");

Route::post("recuperar-cuenta",
    [Authentication::class, "recuperarCuentaPost"]
)
    ->name("recuperarcuentapost");

Route::get("/login", function () {
    return view("cuentaUsuario.login");
})->middleware(["guest"])
    ->name("web-login");

//El login vía web es solo para el admin del sistema, para poder usar las pruebas de correo y telescope
Route::post("/login", function (Request $request) {
    if ($request->email === env("ADMIN_AUTORIZADO") &&
        Auth::attempt($request->only("email", "password"))) {
        return redirect()->route("consesion");
    } else {
        return redirect()->back();
    }
})->middleware(["guest"])->name("web-post-login");


///////////////////////////
///// RUTAS GENERALES /////
///////////////////////////

Route::get("politica-de-privacidad", function () {//TODO
    return view("politicaDePrivacidad");
});

Route::get("tutorial-eliminar-cuenta", function () {//TODO
    return view("tutorialEliminarCuenta");
});


//////////////////////////////
///// RUTAS DE GIMNASIOS /////
//////////////////////////////

Route::get("/gimnasios/{gimnasio}/aceptar-invitacion/{hash}",
    [GimnasioController::class, "aceptarInvitacion"])
->name("aceptar-invitacion");


////////////////////////////
///// RUTAS DE PRUEBAS /////
////////////////////////////

Route::get("prueba-correo", function () {
    User::where("email", env("admin_autorizado"))->first()->notify(new PruebaBorrar());
    return "Correo de prueba enviado";
})
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("esAdmin", User::class);

Route::get("prueba-queued-correo", function () {
    User::where("email", env("admin_autorizado"))->first()->notify(new PruebaQueuedBorrar());
    return "Correo encolado de prueba enviado";
})
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("esAdmin", User::class);

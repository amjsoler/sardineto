<?php

use App\Http\Controllers\GimnasioController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get("/gimnasios/{gimnasio}/aceptar-invitacion/{hash}", [GimnasioController::class, "aceptarInvitacion"])
    ->name("aceptar-invitacion");





///////////////////////////////
/////// RUTAS DE CUENTA ///////
///////////////////////////////

Route::get("/con-sesion", function(){
    return "Tienes una sesión iniciada";
})->middleware("auth:sanctum")->name("consesion");

Route::get("verificar-cuenta/{token}",
    [Authentication::class, "verificarCuentaConToken"]
)->name("verificarcuentacontoken");

Route::get("recuperar-cuenta/{token}",
    [Authentication::class, "recuperarCuentaGet"]
)->name("recuperarcuentaget");

Route::post("recuperar-cuenta",
    [Authentication::class, "recuperarCuentaPost"]
)->name("recuperarcuentapost");

Route::get("/login", function(){
    return view("cuentaUsuario.login");
})->middleware(["guest"])
    ->name("login");

Route::post("/login", function(Request $request){
    if(Auth::attempt(array("email" => $request->get("email"), "password" => $request->get("password")))){
        return redirect(route("versorteos"));
    }else{
        return redirect()->back();
    }
})->middleware(["guest"]);





///////////////////////////
///// RUTAS GENERALES /////
///////////////////////////

Route::get("politica-de-privacidad", function(){
    return view("politicaDePrivacidad");
});

Route::get("tutorial-eliminar-cuenta", function() {
    return view("tutorialEliminarCuenta");
});





////////////////////////////
///// RUTAS DE PRUEBAS /////
////////////////////////////

Route::get("prueba-correo", function(){
    User::where("email", "amjsoler@gmail.com")->first()->notify(new PruebaBorrar());
})->middleware("auth:sanctum", "cuentaVerificada")
    ->can("esAdmin", User::class);

Route::get("prueba-queued-correo", function(){
    User::where("email", "amjsoler@gmail.com")->first()->notify(new PruebaQueuedBorrar());
})->middleware("auth:sanctum", "cuentaVerificada")
    ->can("esAdmin", User::class);

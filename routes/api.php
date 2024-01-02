<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\GimnasioController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\TarifaController;
use App\Models\Articulo;
use App\Models\Clase;
use App\Models\Suscripcion;
use App\Models\Tarifa;
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

Route::get("gimnasios/{gimnasio}/tarifas",
    [TarifaController::class, "verTarifas"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verTarifas", [Tarifa::class, "gimnasio"])
    ->name("ver-tarifas");

Route::post("gimnasios/{gimnasio}/tarifas",
    [TarifaController::class, "crearTarifa"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearTarifas", [Tarifa::class, "gimnasio"])
    ->name("crear-tarifas");

Route::put("gimnasios/{gimnasio}/tarifas/{tarifa}",
    [TarifaController::class, "modificarTarifa"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarTarifas", [Tarifa::class, "gimnasio", "tarifa"])
    ->name("editar-tarifas");

Route::delete("gimnasios/{gimnasio}/tarifas/{tarifa}",
    [TarifaController::class, "eliminarTarifa"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarTarifas", [Tarifa::class, "gimnasio", "tarifa"])
    ->name("eliminar-tarifas");



/////////////////////////
///// SUSCRIPCIONES /////
/////////////////////////

Route::get("gimnasios/{gimnasio}/suscripciones",
    [SuscripcionController::class, "verSuscripciones"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verSuscripciones", [Suscripcion::class, "gimnasio"])
    ->name("ver-suscripcion");

//Generar una suscripción como user
Route::post("gimnasios/{gimnasio}/suscripciones",
    [SuscripcionController::class, "crearSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearSuscripciones", [Suscripcion::class, "gimnasio"])
    ->name("crear-suscripcion");

//Generar suscripción como admin
Route::post("gimnasios/{gimnasio}/suscribirse",
    [SuscripcionController::class, "adminCreaSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearSuscripcionesComoAdmin", [Suscripcion::class, "gimnasio"])
    ->name("admin-crear-suscripcion");

Route::put("gimnasios/{gimnasio}/suscripciones/{suscripcion}",
    [SuscripcionController::class, "editarSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarSuscripciones", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("editar-suscripcion");

Route::delete("gimnasios/{gimnasio}/suscripciones/{suscripcion}",
    [SuscripcionController::class, "eliminarSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarSuscripciones", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("eliminar-suscripcion");

Route::get("gimnasios/{gimnasio}/suscripciones/{suscripcion}/marcar-pagada",
    [SuscripcionController::class, "marcarSuscripcionComoPagada"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("marcarSuscripcionesPagadas", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("marcar-suscripcion-pagada");



/////////////////////
///// ARTÍCULOS /////
/////////////////////

Route::get("gimnasios/{gimnasio}/articulos",
    [ArticuloController::class, "verArticulos"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verArticulos", [Articulo::class, "gimnasio"])
    ->name("ver-articulos");

Route::post("gimnasios/{gimnasio}/articulos",
    [ArticuloController::class, "crearArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearArticulos", [Articulo::class, "gimnasio"])
    ->name("crear-articulo");

Route::put("gimnasios/{gimnasio}/articulos/{articulo}",
    [ArticuloController::class, "editarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("editar-articulo");

Route::delete("gimnasios/{gimnasio}/articulos/{articulo}",
    [ArticuloController::class, "eliminarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("eliminar-articulo");

Route::get("gimnasios/{gimnasio}/articulos/historial-compras",
    [ArticuloController::class, "historialDeCompras"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verMiHistorialDeCompras", [Articulo::class, "gimnasio"])
    ->name("articulos-historial-compras");

Route::get("gimnasios/{gimnasio}/articulos/{articulo}/comprar",
    [ArticuloController::class, "comprarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("comprarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("comprar-articulo");
;
Route::get("gimnasios/{gimnasio}/articulos/pagar-compra/{compra}",
    [ArticuloController::class, "pagarCompra"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("pagarCompras", [Articulo::class, "gimnasio", "compra"])
    ->name("pagar-compra");


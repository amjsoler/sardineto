<?php

use App\Http\Controllers\ApiAuthentication;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\EjercicioController;
use App\Http\Controllers\EjerciciosUsuariosController;
use App\Http\Controllers\GimnasioController;
use App\Http\Controllers\MetricaController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\UserController;
use App\Models\Articulo;
use App\Models\Clase;
use App\Models\Ejercicio;
use App\Models\EjercicioUsuario;
use App\Models\Gimnasio;
use App\Models\Metrica;
use App\Models\Suscripcion;
use App\Models\Tarifa;
use Illuminate\Support\Facades\Route;





//////////////////////////////////////
/////// RUTAS DE AUTENTICACIÓN ///////
//////////////////////////////////////

Route::post("/iniciar-sesion",
    [ApiAuthentication::class, "login"]
)
    ->middleware("invitadoObligatorio")
    ->name("iniciar-sesion");

Route::post("/registrarse",
    [ApiAuthentication::class, "register"]
)
    ->middleware("invitadoObligatorio")
    ->name("registrarse");

Route::post("/recuperar-cuenta",
    [ApiAuthentication::class, "recuperarCuenta"]
)
    ->name("recuperar-cuenta");

Route::get("/verificar-cuenta",
    [ApiAuthentication::class, "mandarCorreoVerificacionCuenta"]
)
    ->middleware("auth:sanctum", "cuentaSinVerificar")
    ->name("verificar-cuenta");

Route::post("/cambiar-contrasena",
    [ApiAuthentication::class, "cambiarContrasena"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("cambiar-contrasena");

Route::post("/ajustes-cuenta",
    [UserController::class, "guardarAjustesCuentaUsuario"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("guardar-ajustes-cuenta");

Route::delete("/eliminar-cuenta",
    [UserController::class, "eliminarCuenta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("eliminar-cuenta");

Route::post("/enviar-sugerencia",
    [UserController::class, "enviarSugerencia"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("enviar-sugerencia");





/////////////////////
///// GIMNASIOS /////
/////////////////////

Route::get("gimnasios",
    [GimnasioController::class, "misGimnasios"]
)
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->name("mis-gimnasios");

Route::post("gimnasios",
    [GimnasioController::class, "crearGimnasio"]
)
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->name("crear-gimnasio");

Route::put("gimnasios/{gimnasio}",
    [GimnasioController::class, "editarGimnasio"]
)
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->can("editarGimnasio", "gimnasio")
    ->name("editar-gimnasio");

Route::delete("gimnasios/{gimnasio}",
    [GimnasioController::class, "eliminarGimnasio"]
)
    ->middleware(["auth:sanctum", "cuentaVerificada"])
    ->can("eliminarGimnasio", "gimnasio")
    ->name("eliminar-gimnasio");

Route::post("gimnasios/{gimnasio}/invitar-usuario",
    [GimnasioController::class, "invitarUsuario"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("invitarUsuarios", "gimnasio")
    ->name("invitar-usuario");

Route::get("gimnasios/{gimnasio}/reenviar-invitacion/{usuario}",
    [GimnasioController::class, "reenviarInvitacion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("reenviarInvitaciones", "gimnasio")
    ->name("reenviar-invitacion");

Route::get("gimnasios/{gimnasio}/crear-administrador/{usuario}",
    [GimnasioController::class, "anyadirAdministrador"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearAdministradores", [Gimnasio::class, "gimnasio", "usuario"])
    ->name("crear-administrador");

Route::delete("gimnasios/{gimnasio}/quitar-administrador/{usuario}",
    [GimnasioController::class, "quitarAdministrador"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("quitarAdministradores", [Gimnasio::class, "gimnasio", "usuario"])
    ->name("quitar-administrador");





//////////////////
///// CLASES /////
//////////////////

Route::get("gimnasios/{gimnasio}/clases",
    [ClaseController::class, "verClases"]
)
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

Route::get("gimnasios/{gimnasio}/clases/{clase}/apuntarse", //TODO TESTING
    [ClaseController::class, "usuarioSeApunta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    //TODO MIDDLEWARE: comprobar si el user tiene créditos para apuntarse
    //TODO MIDDLEWARE: comprobar si quedan plazas para apuntarse
    ->can("usuarioSePuedeApuntar", [Clase::class, "gimnasio", "clase"])
    ->name("usuario-se-apunta");

Route::get("gimnasios/{gimnasio}/clases/{clase}/desapuntarse", //TODO TESTING
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
    ->name("ver-suscripciones");

//Generar una suscripción como user
Route::post("gimnasios/{gimnasio}/suscripciones",
    [SuscripcionController::class, "crearSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearSuscripciones", [Suscripcion::class, "gimnasio"])
    ->name("crear-suscripcion");

//Generar suscripción como admin
Route::post("gimnasios/{gimnasio}/suscribir-usuario",
    [SuscripcionController::class, "adminCreaSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearSuscripcionesComoAdmin", [Suscripcion::class, "gimnasio"])
    ->name("admin-crea-suscripcion");

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

Route::get("gimnasios/{gimnasio}/articulos/pagar-compra/{compra}",
    [ArticuloController::class, "pagarCompra"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("pagarCompras", [Articulo::class, "gimnasio", "compra"])
    ->name("pagar-compra");

Route::get("gimnasios/{gimnasio}/articulos/entregar-articulo/{compra}",
    [ArticuloController::class, "entregarCompra"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("entregarCompras", [Articulo::class, "gimnasio", "compra"])
    ->name("entregar-compra");





//////////////////////
///// EJERCICIOS /////
//////////////////////

Route::get("gimnasios/{gimnasio}/ejercicios",
    [EjercicioController::class, "verEjercicios"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verEjercicios", [Ejercicio::class, "gimnasio"])
    ->name("ver-ejercicios");

Route::post("gimnasios/{gimnasio}/ejercicios",
    [EjercicioController::class, "crearEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearEjercicios", [Ejercicio::class, "gimnasio"])
    ->name("crear-ejercicio");

Route::put("gimnasios/{gimnasio}/ejercicios/{ejercicio}",
    [EjercicioController::class, "modificarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("modificarEjercicios", [Ejercicio::class, "gimnasio", "ejercicio"])
    ->name("modificar-ejercicio");

Route::delete("gimnasios/{gimnasio}/ejercicios/{ejercicio}",
    [EjercicioController::class, "eliminarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarEjercicios", [Ejercicio::class, "gimnasio", "ejercicio"])
    ->name("eliminar-ejercicio");

Route::post("gimnasios/{gimnasio}/clases/{clase}/asignar-ejercicio/{ejercicio}",
    [EjercicioController::class, "asociarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("asociarEjerciciosAClase", [Ejercicio::class, "gimnasio", "clase", "ejercicio"])
    ->name("asociar-ejercicio");

Route::get("gimnasios/{gimnasio}/clases/{clase}/desasignar-ejercicio/{ejercicio}",
    [EjercicioController::class, "desasociarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("desasociarEjerciciosAClase", [Ejercicio::class, "gimnasio", "clase", "ejercicio"])
    ->name("desasociar-ejercicio");





////////////////////
///// MÉTRICAS /////
////////////////////

Route::get("metricas",
    [MetricaController::class, "verMetricas"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("ver-metricas");

Route::post("metricas",
    [MetricaController::class, "crearMetrica"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("crear-metrica");

Route::delete("metricas/{metrica}",
    [MetricaController::class, "eliminarMetrica"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarMetricas", [Metrica::class, "metrica"])
    ->name("eliminar-metrica");





////////////////////////////
///// EJERCICIOUSUARIO /////
////////////////////////////

Route::get("gimnasios/{gimnasio}/registros-de-peso",
    [EjerciciosUsuariosController::class, "verRegistrosDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verRegistrosDePeso", [EjercicioUsuario::class, "gimnasio"])
    ->name("ver-registros-de-peso");

Route::get("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso",
    [EjerciciosUsuariosController::class, "verRegistrosDePesoPorEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verRegistrosDePesoPorEjercicio", [EjercicioUsuario::class, "gimnasio", "ejercicio"])
    ->name("ver-registros-de-peso-por-ejercicio");

Route::post("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso",
    [EjerciciosUsuariosController::class, "registrarNuevaMarcaDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearRegistrosDePeso", [EjercicioUsuario::class, "gimnasio", "ejercicio"])
    ->name("crear-registros-de-peso");

Route::delete("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso/{ejercicioUsuario}",
    [EjerciciosUsuariosController::class, "eliminarMarcaDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarRegistrosDePeso", [EjercicioUsuario::class, "gimnasio", "ejercicio", "ejercicioUsuario"])
    ->name("eliminar-registros-de-peso");

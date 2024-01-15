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

Route::post("/iniciar-sesion", //TODO TESTING
    [ApiAuthentication::class, "login"]
)
    ->middleware("guest");

Route::post("/registrarse", //TODO TESTING
    [ApiAuthentication::class, "register"]
)
    ->middleware("guest");

Route::post("/recuperar-cuenta", //TODO TESTING
    [ApiAuthentication::class, "recuperarCuenta"]
);

Route::get("/verificar-cuenta", //TODO TESTING
    [ApiAuthentication::class, "mandarCorreoVerificacionCuenta"]
)
    ->middleware("auth:sanctum");

Route::post("/cambiar-contrasena", //TODO TESTING
    [ApiAuthentication::class, "cambiarContrasena"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");

Route::post("/ajustes-cuenta", //TODO TESTING
    [UserController::class, "guardarAjustesCuentaUsuario"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");

Route::delete("/eliminar-cuenta", //TODO TESTING
    [UserController::class, "eliminarCuenta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");

Route::post("/enviar-sugerencia", //TODO TESTING
    [UserController::class, "enviarSugerencia"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");





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

Route::get("gimnasios/{gimnasio}/clases", //TODO TESTING
    [ClaseController::class, "verClases"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verClases", [Clase::class, "gimnasio"])
    ->name("ver-clases-de-gimnasio");

Route::post("gimnasios/{gimnasio}/clases", //TODO TESTING
    [ClaseController::class, "crearClase"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearClases", [Clase::class, "gimnasio"])
    ->name("crear-clase");

Route::put("gimnasios/{gimnasio}/clases/{clase}", //TODO TESTING
    [ClaseController::class, "editarClase"]
)->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarClases", [Clase::class, "gimnasio", "clase"])
    ->name("editar-clase");

Route::delete("gimnasios/{gimnasio}/clases/{clase}", //TODO TESTING
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

Route::get("gimnasios/{gimnasio}/suscripciones", //TODO TESTING
    [SuscripcionController::class, "verSuscripciones"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verSuscripciones", [Suscripcion::class, "gimnasio"])
    ->name("ver-suscripcion");

//Generar una suscripción como user
Route::post("gimnasios/{gimnasio}/suscripciones", //TODO TESTING
    [SuscripcionController::class, "crearSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearSuscripciones", [Suscripcion::class, "gimnasio"])
    //TODO COMPROBAR POR MIDDLEWARE SI TIENES UNA SUSCRIPCIÓN ACTIVA Y NO ES ABONO
    ->name("crear-suscripcion");

//Generar suscripción como admin
Route::post("gimnasios/{gimnasio}/suscribirse", //TODO TESTING
    [SuscripcionController::class, "adminCreaSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    //TODO COMPROBAR POR MIDDLEWARE SI TIENES UNA SUSCRIPCIÓN ACTIVA Y NO ES ABONO
    ->can("crearSuscripcionesComoAdmin", [Suscripcion::class, "gimnasio"])
    ->name("admin-crear-suscripcion");

Route::put("gimnasios/{gimnasio}/suscripciones/{suscripcion}", //TODO TESTING
    [SuscripcionController::class, "editarSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarSuscripciones", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("editar-suscripcion");

Route::delete("gimnasios/{gimnasio}/suscripciones/{suscripcion}", //TODO TESTING
    [SuscripcionController::class, "eliminarSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarSuscripciones", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("eliminar-suscripcion");

Route::get("gimnasios/{gimnasio}/suscripciones/{suscripcion}/marcar-pagada", //TODO TESTING
    [SuscripcionController::class, "marcarSuscripcionComoPagada"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("marcarSuscripcionesPagadas", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("marcar-suscripcion-pagada");





/////////////////////
///// ARTÍCULOS /////
/////////////////////

Route::get("gimnasios/{gimnasio}/articulos", //TODO TESTING
    [ArticuloController::class, "verArticulos"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verArticulos", [Articulo::class, "gimnasio"])
    ->name("ver-articulos");

Route::post("gimnasios/{gimnasio}/articulos", //TODO TESTING
    [ArticuloController::class, "crearArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearArticulos", [Articulo::class, "gimnasio"])
    ->name("crear-articulo");

Route::put("gimnasios/{gimnasio}/articulos/{articulo}", //TODO TESTING
    [ArticuloController::class, "editarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("editar-articulo");

Route::delete("gimnasios/{gimnasio}/articulos/{articulo}", //TODO TESTING
    [ArticuloController::class, "eliminarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("eliminar-articulo");

Route::get("gimnasios/{gimnasio}/articulos/historial-compras", //TODO TESTING
    [ArticuloController::class, "historialDeCompras"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verMiHistorialDeCompras", [Articulo::class, "gimnasio"])
    ->name("articulos-historial-compras");

Route::get("gimnasios/{gimnasio}/articulos/{articulo}/comprar", //TODO TESTING
    [ArticuloController::class, "comprarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("comprarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("comprar-articulo");

Route::get("gimnasios/{gimnasio}/articulos/pagar-compra/{compra}", //TODO TESTING
    [ArticuloController::class, "pagarCompra"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("pagarCompras", [Articulo::class, "gimnasio", "compra"])
    ->name("pagar-compra");





//////////////////////
///// EJERCICIOS /////
//////////////////////

Route::get("gimnasios/{gimnasio}/ejercicios", //TODO TESTING
    [EjercicioController::class, "verEjercicios"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verEjercicios", [Ejercicio::class, "gimnasio"])
    ->name("ver-ejercicios");

Route::post("gimnasios/{gimnasio}/ejercicios", //TODO TESTING
    [EjercicioController::class, "crearEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearEjercicios", [Ejercicio::class, "gimnasio"])
    ->name("crear-ejercicio");

Route::put("gimnasios/{gimnasio}/ejercicios/{ejercicio}", //TODO TESTING
    [EjercicioController::class, "modificarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("modificarEjercicios", [Ejercicio::class, "gimnasio", "ejercicio"])
    ->name("modificar-ejercicio");

Route::delete("gimnasios/{gimnasio}/ejercicios/{ejercicio}", //TODO TESTING
    [EjercicioController::class, "eliminarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarEjercicios", [Ejercicio::class, "gimnasio", "ejercicio"])
    ->name("eliminar-ejercicio");

Route::get("gimnasios/{gimnasio}/clases/{clase}/asignar-ejercicio/{ejercicio}", //TODO TESTING
    [EjercicioController::class, "asociarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("asociarEjerciciosAClase", [Ejercicio::class, "gimnasio", "clase", "ejercicio"])
    ->name("asociar-ejercicio");

Route::get("gimnasios/{gimnasio}/clases/{clase}/desasignar-ejercicio/{ejercicio}", //TODO TESTING
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

Route::get("gimnasios/{gimnasio}/registros-de-peso", //TODO TESTING
    [EjerciciosUsuariosController::class, "verRegistrosDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verRegistrosDePeso", [EjercicioUsuario::class, "gimnasio"])
    ->name("ver-registros-de-peso");

Route::get("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso", //TODO TESTING
    [EjerciciosUsuariosController::class, "verRegistrosDePesoPorEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verRegistrosDePesoPorEjercicio", [EjercicioUsuario::class, "gimnasio", "ejercicio"])
    ->name("ver-registros-de-peso-por-ejercicio");

Route::post("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso", //TODO TESTING
    [EjerciciosUsuariosController::class, "registrarNuevaMarcaDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearRegistrosDePeso", [EjercicioUsuario::class, "gimnasio", "ejercicio"])
    ->name("crear-registros-de-peso");

Route::delete("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso/{ejercicioUsuario}", //TODO TESTING
    [EjerciciosUsuariosController::class, "eliminarMarcaDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarRegistrosDePeso", [EjercicioUsuario::class, "gimnasio", "ejercicio", "ejercicioUsuario"])
    ->name("eliminar-registros-de-peso");

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
    ->middleware("guest");

Route::post("/registrarse",
    [ApiAuthentication::class, "register"]
)
    ->middleware("guest");

Route::post("/recuperar-cuenta",
    [ApiAuthentication::class, "recuperarCuenta"]
);

Route::get("/verificar-cuenta",
    [ApiAuthentication::class, "mandarCorreoVerificacionCuenta"]
)
    ->middleware("auth:sanctum");

Route::post("/cambiar-contrasena",
    [ApiAuthentication::class, "cambiarContrasena"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");

Route::post("/ajustes-cuenta",
    [UserController::class, "guardarAjustesCuentaUsuario"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");

Route::delete("/eliminar-cuenta",
    [UserController::class, "eliminarCuenta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");

Route::post("/enviar-sugerencia",
    [UserController::class, "enviarSugerencia"]
)
    ->middleware("auth:sanctum", "cuentaVerificada");





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

Route::post("gimnasios/{gimnasio}/invitar-usuario",////TODO
    [GimnasioController::class, "invitarUsuario"])
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("invitarUsuarios", "gimnasio")
    ->name("invitar-usuario");

Route::get("gimnasios/{gimnasio}/reenviar-invitacion/{usuario}",////TODO
    [GimnasioController::class, "reenviarInvitacion"])
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("reenviarInvitaciones", "gimnasio")
    ->name("reenviar-invitacion");

Route::get("gimnasios/{gimnasio}/crear-administrador/{usuario}",////TODO
    [GimnasioController::class, "anyadirAdministrador"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearAdministradores", [Gimnasio::class, "gimnasio", "usuario"])
    ->name("crear-administrador");

Route::delete("gimnasios/{gimnasio}/quitar-administrador/{usuario}",////TODO
    [GimnasioController::class, "quitarAdministrador"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("quitarAdministradores", [Gimnasio::class, "gimnasio", "usuario"])
    ->name("quitar-administrador");





//////////////////
///// CLASES /////
//////////////////

Route::get("gimnasios/{gimnasio}/clases",////TODO
    [ClaseController::class, "verClases"])
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verClases", [Clase::class, "gimnasio"])
    ->name("ver-clases-de-gimnasio");

Route::post("gimnasios/{gimnasio}/clases",////TODO
    [ClaseController::class, "crearClase"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearClases", [Clase::class, "gimnasio"])
    ->name("crear-clase");

Route::put("gimnasios/{gimnasio}/clases/{clase}",////TODO
    [ClaseController::class, "editarClase"]
)->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarClases", [Clase::class, "gimnasio", "clase"])
    ->name("editar-clase");

Route::delete("gimnasios/{gimnasio}/clases/{clase}",////TODO
    [ClaseController::class, "eliminarClase"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarClases", [Clase::class, "gimnasio", "clase"])
    ->name("eliminar-clase");

Route::get("gimnasios/{gimnasio}/clases/{clase}/apuntarse",////TODO
    [ClaseController::class, "usuarioSeApunta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("usuarioSePuedeApuntar", [Clase::class, "gimnasio", "clase"])
    ->name("usuario-se-apunta");

Route::get("gimnasios/{gimnasio}/clases/{clase}/desapuntarse",////TODO
    [ClaseController::class, "usuarioSeDesapunta"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("usuarioSePuedeDesapuntar", [Clase::class, "gimnasio", "clase"])
    ->name("usuario-se-desapunta");





///////////////////
///// TARIFAS /////
///////////////////

Route::get("gimnasios/{gimnasio}/tarifas",////TODO
    [TarifaController::class, "verTarifas"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verTarifas", [Tarifa::class, "gimnasio"])
    ->name("ver-tarifas");

Route::post("gimnasios/{gimnasio}/tarifas",////TODO
    [TarifaController::class, "crearTarifa"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearTarifas", [Tarifa::class, "gimnasio"])
    ->name("crear-tarifas");

Route::put("gimnasios/{gimnasio}/tarifas/{tarifa}",////TODO
    [TarifaController::class, "modificarTarifa"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarTarifas", [Tarifa::class, "gimnasio", "tarifa"])
    ->name("editar-tarifas");

Route::delete("gimnasios/{gimnasio}/tarifas/{tarifa}",////TODO
    [TarifaController::class, "eliminarTarifa"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarTarifas", [Tarifa::class, "gimnasio", "tarifa"])
    ->name("eliminar-tarifas");





/////////////////////////
///// SUSCRIPCIONES /////
/////////////////////////

Route::get("gimnasios/{gimnasio}/suscripciones",////TODO
    [SuscripcionController::class, "verSuscripciones"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verSuscripciones", [Suscripcion::class, "gimnasio"])
    ->name("ver-suscripcion");

//Generar una suscripción como user
Route::post("gimnasios/{gimnasio}/suscripciones",////TODO
    [SuscripcionController::class, "crearSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearSuscripciones", [Suscripcion::class, "gimnasio"])
    ->name("crear-suscripcion");

//Generar suscripción como admin
Route::post("gimnasios/{gimnasio}/suscribirse",////TODO
    [SuscripcionController::class, "adminCreaSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearSuscripcionesComoAdmin", [Suscripcion::class, "gimnasio"])
    ->name("admin-crear-suscripcion");

Route::put("gimnasios/{gimnasio}/suscripciones/{suscripcion}",////TODO
    [SuscripcionController::class, "editarSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarSuscripciones", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("editar-suscripcion");

Route::delete("gimnasios/{gimnasio}/suscripciones/{suscripcion}",////TODO
    [SuscripcionController::class, "eliminarSuscripcion"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarSuscripciones", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("eliminar-suscripcion");

Route::get("gimnasios/{gimnasio}/suscripciones/{suscripcion}/marcar-pagada",////TODO
    [SuscripcionController::class, "marcarSuscripcionComoPagada"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("marcarSuscripcionesPagadas", [Suscripcion::class, "gimnasio", "suscripcion"])
    ->name("marcar-suscripcion-pagada");





/////////////////////
///// ARTÍCULOS /////
/////////////////////

Route::get("gimnasios/{gimnasio}/articulos",////TODO
    [ArticuloController::class, "verArticulos"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verArticulos", [Articulo::class, "gimnasio"])
    ->name("ver-articulos");

Route::post("gimnasios/{gimnasio}/articulos",////TODO
    [ArticuloController::class, "crearArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearArticulos", [Articulo::class, "gimnasio"])
    ->name("crear-articulo");

Route::put("gimnasios/{gimnasio}/articulos/{articulo}",////TODO
    [ArticuloController::class, "editarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("editarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("editar-articulo");

Route::delete("gimnasios/{gimnasio}/articulos/{articulo}",////TODO
    [ArticuloController::class, "eliminarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("eliminar-articulo");

Route::get("gimnasios/{gimnasio}/articulos/historial-compras",////TODO
    [ArticuloController::class, "historialDeCompras"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verMiHistorialDeCompras", [Articulo::class, "gimnasio"])
    ->name("articulos-historial-compras");

Route::get("gimnasios/{gimnasio}/articulos/{articulo}/comprar",////TODO
    [ArticuloController::class, "comprarArticulo"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("comprarArticulos", [Articulo::class, "gimnasio", "articulo"])
    ->name("comprar-articulo");

Route::get("gimnasios/{gimnasio}/articulos/pagar-compra/{compra}",////TODO
    [ArticuloController::class, "pagarCompra"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("pagarCompras", [Articulo::class, "gimnasio", "compra"])
    ->name("pagar-compra");





//////////////////////
///// EJERCICIOS /////
//////////////////////

Route::get("gimnasios/{gimnasio}/ejercicios",////TODO
    [EjercicioController::class, "verEjercicios"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verEjercicios", [Ejercicio::class, "gimnasio"])
    ->name("ver-ejercicios");

Route::post("gimnasios/{gimnasio}/ejercicios",////TODO
    [EjercicioController::class, "crearEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearEjercicios", [Ejercicio::class, "gimnasio"])
    ->name("crear-ejercicio");

Route::put("gimnasios/{gimnasio}/ejercicios/{ejercicio}",////TODO
    [EjercicioController::class, "modificarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("modificarEjercicios", [Ejercicio::class, "gimnasio", "ejercicio"])
    ->name("modificar-ejercicio");

Route::delete("gimnasios/{gimnasio}/ejercicios/{ejercicio}",////TODO
    [EjercicioController::class, "eliminarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarEjercicios", [Ejercicio::class, "gimnasio", "ejercicio"])
    ->name("eliminar-ejercicio");

Route::get("gimnasios/{gimnasio}/clases/{clase}/asignar-ejercicio/{ejercicio}",////TODO
    [EjercicioController::class, "asociarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("asociarEjerciciosAClase", [Ejercicio::class, "gimnasio", "clase", "ejercicio"])
    ->name("asociar-ejercicio");

Route::get("gimnasios/{gimnasio}/clases/{clase}/desasignar-ejercicio/{ejercicio}",////TODO
    [EjercicioController::class, "desasociarEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("desasociarEjerciciosAClase", [Ejercicio::class, "gimnasio", "clase", "ejercicio"])
    ->name("desasociar-ejercicio");





////////////////////
///// MÉTRICAS /////
////////////////////

Route::get("metricas",////TODO
    [MetricaController::class, "verMetricas"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("ver-metricas");

Route::post("metricas",////TODO
    [MetricaController::class, "crearMetrica"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->name("crear-metrica");

Route::delete("metricas/{metrica}",////TODO
    [MetricaController::class, "eliminarMetrica"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarMetricas", [Metrica::class, "metrica"])
    ->name("eliminar-metrica");





////////////////////////////
///// EJERCICIOUSUARIO /////
////////////////////////////

Route::get("gimnasios/{gimnasio}/registros-de-peso",////TODO
    [EjerciciosUsuariosController::class, "verRegistrosDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verRegistrosDePeso", [EjercicioUsuario::class, "gimnasio"])
    ->name("ver-registros-de-peso");

Route::get("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso",////TODO
    [EjerciciosUsuariosController::class, "verRegistrosDePesoPorEjercicio"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("verRegistrosDePesoPorEjercicio", [EjercicioUsuario::class, "gimnasio", "ejercicio"])
    ->name("ver-registros-de-peso-por-ejercicio");

Route::post("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso",////TODO
    [EjerciciosUsuariosController::class, "registrarNuevaMarcaDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("crearRegistrosDePeso", [EjercicioUsuario::class, "gimnasio", "ejercicio"])
    ->name("crear-registros-de-peso");

Route::delete("gimnasios/{gimnasio}/ejercicios/{ejercicio}/registros-de-peso/{ejercicioUsuario}",////TODO
    [EjerciciosUsuariosController::class, "eliminarMarcaDePeso"]
)
    ->middleware("auth:sanctum", "cuentaVerificada")
    ->can("eliminarRegistrosDePeso", [EjercicioUsuario::class, "gimnasio", "ejercicio", "ejercicioUsuario"])
    ->name("eliminar-registros-de-peso");

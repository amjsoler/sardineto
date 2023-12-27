<?php

use App\Http\Controllers\GimnasioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/gimnasios/{gimnasio}/aceptar-invitacion/{hash}", [GimnasioController::class, "aceptarInvitacion"])
    ->name("aceptar-invitacion");

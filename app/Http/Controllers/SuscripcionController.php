<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuscripcionCrearSuscripcionComoAdminRequest;
use App\Http\Requests\SuscripcionCrearSuscripcionRequest;
use App\Http\Requests\SuscripcionEditarSuscripcionRequest;
use App\Models\Gimnasio;
use App\Models\Suscripcion;

class SuscripcionController extends Controller
{
    public function verSuscripciones(Gimnasio $gimnasio)
    {
        return response()->json($gimnasio->suscripciones);
    }

    public function crearSuscripcion(Gimnasio $gimnasio, SuscripcionCrearSuscripcionRequest $request)
    {
        $suscripcion = Suscripcion::make($request->all());
        $suscripcion->usuario = auth()->user()->id;
        $suscripcion->gimnasio = $gimnasio->id;
        $suscripcion->save();

        return response()->json($suscripcion->refresh());
    }

    public function adminCreaSuscripcion(Gimnasio $gimnasio, SuscripcionCrearSuscripcionComoAdminRequest $request)
    {
        $suscripcion = Suscripcion::make($request->all());
        $suscripcion->gimnasio = $gimnasio->id;
        $suscripcion->save();

        return response()->json($suscripcion->refresh());
    }

    public function editarSuscripcion(Gimnasio $gimnasio, Suscripcion $suscripcion, SuscripcionEditarSuscripcionRequest $request)
    {
        $suscripcion->update($request->all());

        return response()->json($suscripcion);
    }

    public function eliminarSuscripcion(Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        $suscripcion->delete();

        return response()->json();
    }

    public function marcarSuscripcionComoPagada(Gimnasio $gimnasio, Suscripcion $suscripcion)
    {
        $suscripcion->pagada = now();
        $suscripcion->save();

        return response()->json();
    }
}

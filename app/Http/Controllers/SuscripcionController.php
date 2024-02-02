<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuscripcionCrearSuscripcionComoAdminRequest;
use App\Http\Requests\SuscripcionCrearSuscripcionRequest;
use App\Http\Requests\SuscripcionEditarSuscripcionRequest;
use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\Tarifa;
use App\Models\User;

class SuscripcionController extends Controller
{
    public function verSuscripciones(Gimnasio $gimnasio)
    {
        return response()->json($gimnasio->suscripciones);
    }

    public function verMisSuscripciones(Gimnasio $gimnasio) {
        return response()->json(
            $gimnasio->suscripciones()
                ->with("tarifaALaQuePertenece")
                ->where("usuario", auth()->user()->getAuthIdentifier())
                ->orderBy("id", "desc")
                ->get()
                ->makeVisible("created_at")
        );
    }

    public function crearSuscripcion(Gimnasio $gimnasio, SuscripcionCrearSuscripcionRequest $request)
    {
        $tarifa = Tarifa::find($request->tarifa);

        $suscripcion = Suscripcion::make($request->all());
        $suscripcion->usuario = auth()->user()->id;
        $suscripcion->creditos_restantes = $tarifa->creditos;
        $gimnasio->suscripciones()->save($suscripcion);

        return response()->json($suscripcion->refresh());
    }

    public function adminCreaSuscripcion(Gimnasio $gimnasio, SuscripcionCrearSuscripcionComoAdminRequest $request)
    {
        $tarifa = Tarifa::find($request->tarifa);

        $suscripcion = Suscripcion::make();
        $suscripcion->tarifa = $request->tarifa;
        $suscripcion->usuario = $request->usuario;
        $suscripcion->creditos_restantes = $tarifa->creditos;
        $gimnasio->suscripciones()->save($suscripcion);

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
        if(!$suscripcion->pagada){
            $suscripcion->pagada = now();
            $suscripcion->save();
        }

        return response()->json();
    }
}

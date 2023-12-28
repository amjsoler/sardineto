<?php

namespace App\Http\Controllers;


use App\Http\Requests\TarifaCrearTarifaRequest;
use App\Http\Requests\TarifaEditarTarifaRequest;
use App\Models\Gimnasio;
use App\Models\Tarifa;

class TarifaController extends Controller
{
    public function verTarifas(Gimnasio $gimnasio)
    {
        return response()->json($gimnasio->tarifas);
    }

    public function crearTarifa(Gimnasio $gimnasio, TarifaCrearTarifaRequest $request)
    {
        $tarifa = Tarifa::make($request->all());
        $tarifa->gimnasio = $gimnasio->id;
        $tarifa->save();

        return response()->json($tarifa);
    }

    public function modificarTarifa(Gimnasio $gimnasio, Tarifa $tarifa, TarifaEditarTarifaRequest $request)
    {
        $tarifa->update($request->all());

        return response()->json($tarifa);
    }

    public function eliminarTarifa(Gimnasio $gimnasio, Tarifa $tarifa)
    {
        $tarifa->delete();

        return response()->json();
    }
}

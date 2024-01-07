<?php

namespace App\Http\Controllers;

use App\Http\Requests\MetricaCrearmetricaRequest;
use App\Models\Metrica;
use Illuminate\Http\Request;

class MetricaController extends Controller
{
    public function verMetricas()
    {
        return response()->json(auth()->user()->metricas);
    }

    public function crearMetrica(MetricaCrearmetricaRequest $request)
    {
        $metrica = Metrica::make($request->all());
        $metrica->usuario = auth()->user()->id;
        $metrica->save();

        return response()->json($metrica);
    }

    public function eliminarMetrica(Metrica $metrica)
    {
        $metrica->delete();

        return response()->json();
    }
}

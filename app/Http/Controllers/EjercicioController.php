<?php

namespace App\Http\Controllers;

use App\Http\Requests\EjercicioAsociarEjercicioAClaseRequest;
use App\Http\Requests\EjercicioCrearEjercicioRequest;
use App\Http\Requests\EjercicioModificarEjercicioRequest;
use App\Models\Clase;
use App\Models\Ejercicio;
use App\Models\Gimnasio;

class EjercicioController extends Controller
{
    public function verEjercicios(Gimnasio $gimnasio)
    {
        return response()->json($gimnasio->ejercicios()->orderBy("nombre", "asc")->get());
    }

    public function crearEjercicio(Gimnasio $gimnasio, EjercicioCrearEjercicioRequest $request)
    {
        $ejercicio = Ejercicio::make($request->all());
        $ejercicio->gimnasio = $gimnasio->id;
        $ejercicio->save();

        return response()->json($ejercicio);
    }

    public function modificarEjercicio(Gimnasio $gimnasio, Ejercicio $ejercicio, EjercicioModificarEjercicioRequest $request)
    {
        $ejercicio->update($request->all());

        return response()->json($ejercicio);
    }

    public function eliminarEjercicio(Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        $ejercicio->delete();

        return response()->json();
    }

    public function asociarEjercicio(Gimnasio $gimnasio, Clase $clase, Ejercicio $ejercicio, EjercicioAsociarEjercicioAClaseRequest $request)
    {
        $clase->ejercicios()->attach($ejercicio, ["gimnasio" => $gimnasio->id, "detalles" => $request->detalles]);

        return response()->json($clase->load("ejercicios"));
    }

    public function desasociarEjercicio(Gimnasio $gimnasio, Clase $clase, Ejercicio $ejercicio)
    {
        $clase->ejercicios()->detach($ejercicio);

        return response()->json($clase->load("ejercicios"));
    }
}

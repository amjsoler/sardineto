<?php

namespace App\Http\Controllers;

use App\Http\Requests\EjerciciosUsuariosCrearEjerciciosUsuariosRequest;
use App\Models\Ejercicio;
use App\Models\EjercicioUsuario;
use App\Models\Gimnasio;

class EjerciciosUsuariosController extends Controller
{
    public function verRegistrosDePeso(Gimnasio $gimnasio)
    {
        return response()->json(auth()->user()->registrosPeso()
            ->with("ejercicio")
            ->whereHas("ejercicio", function($query) use ($gimnasio){
                return $query->where("gimnasio", $gimnasio->id);
            })
            ->get());
    }

    public function verRegistrosDePesoPorEjercicio(Gimnasio $gimnasio, Ejercicio $ejercicio)
    {
        return response()->json(
            auth()->user()
            ->registrosPeso()
            ->where("ejercicio", $ejercicio->id)
            ->get()
        );
    }

    public function registrarNuevaMarcaDePeso(Gimnasio $gimnasio, Ejercicio $ejercicio, EjerciciosUsuariosCrearEjerciciosUsuariosRequest $request)
    {
        $marca = EjercicioUsuario::make($request->all());
        $marca->ejercicio = $ejercicio->id;
        $marca->usuario = auth()->user()->id;
        $marca->save();

        return response()->json($marca);
    }

    public function eliminarMarcaDePeso(Gimnasio $gimnasio, Ejercicio $ejercicio, EjercicioUsuario $ejercicioUsuario)
    {
        $ejercicioUsuario->delete();

        return response()->json();
    }
}

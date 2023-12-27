<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaseCrearClaseRequest;
use App\Http\Requests\ClaseEditarClaseRequest;
use App\Http\Requests\ClaseUsuarioSeApuntaRequest;
use App\Models\Clase;
use App\Models\Gimnasio;

class ClaseController extends Controller
{
    public function verClases(Gimnasio $gimnasio)
    {
        return response()->json($gimnasio->clases);
    }

    public function crearClase(Gimnasio $gimnasio, ClaseCrearClaseRequest $request)
    {
        $clase = new Clase($request->all());
        $clase->gimnasio = $gimnasio->id;
        $clase->save();

        return response()->json($clase);
    }

    public function editarClase(Gimnasio $gimnasio, Clase $clase, ClaseEditarClaseRequest $request)
    {
        $clase->update($request->all());

        return response()->json($clase);
    }

    public function eliminarClase(Gimnasio $gimnasio, Clase $clase)
    {
        $clase->delete();

        return response()->json();
    }

    public function usuarioSeApunta(Gimnasio $gimnasio, Clase $clase, ClaseUsuarioSeApuntaRequest $request)
    {
        $clase->participantes()->attach(auth()->user());

        return response()->json();
    }

    public function usuarioSeDesapunta(Gimnasio $gimnasio, Clase $clase)
    {
        $clase->participantes()->detach(auth()->user()->id);

        return response()->json();
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Requests\ClaseCrearClaseRequest;
use App\Http\Requests\ClaseDesapuntarseDeClaseRequest;
use App\Http\Requests\ClaseEditarClaseRequest;
use App\Http\Requests\ClaseUsuarioSeApuntaRequest;
use App\Models\Clase;
use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\User;

class ClaseController extends Controller
{
    public function verClases(Gimnasio $gimnasio)
    {
        return response()->json($gimnasio->clases);
    }

    public function crearClase(Gimnasio $gimnasio, ClaseCrearClaseRequest $request)
    {
        $clase = Clase::make($request->all());
        $gimnasio->clases()->save($clase);

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
        $suscripcionActiva = Helpers::dameSuscripcionActivaOAbonoDeUsuario(User::find(auth()->user()->id), $gimnasio);

        $clase->participantes()->attach(auth()->user(), ["suscripcion" => $suscripcionActiva->id]);

        //Decrementamos los créditos restantes de la suscripción
        $suscripcionActiva->creditos_restantes = $suscripcionActiva->creditos_restantes-1;
        $suscripcionActiva->save();

        return response()->json();
    }

    public function usuarioSeDesapunta(Gimnasio $gimnasio, Clase $clase, ClaseDesapuntarseDeClaseRequest $request)
    {
        $suscripcionActiva = Suscripcion::find($clase->participantes()->withPivot("suscripcion")->wherePivot("usuario", auth()->user()->id)->first()->pivot->suscripcion);

        $clase->participantes()->detach(auth()->user()->id);

        $suscripcionActiva->creditos_restantes = $suscripcionActiva->creditos_restantes+1;
        $suscripcionActiva->save();

        return response()->json();
    }
}

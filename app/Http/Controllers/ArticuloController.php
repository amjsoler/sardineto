<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticuloCrearArticuloRequest;
use App\Http\Requests\ArticuloEditarArticuloRequest;
use App\Models\Articulo;
use App\Models\Gimnasio;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function verArticulos(Gimnasio $gimnasio)
    {
        return response()->json($gimnasio->articulos);
    }

    public function crearArticulo(Gimnasio $gimnasio, ArticuloCrearArticuloRequest $request)
    {
        $articulo = Articulo::make($request->all());
        $articulo->gimnasio = $gimnasio->id;
        $articulo->save();

        return response()->json($articulo);
    }

    public function editarArticulo(Gimnasio $gimnasio, Articulo $articulo, ArticuloEditarArticuloRequest $request)
    {
        $articulo->update($request->all());

        return response()->json($articulo);
    }

    public function eliminarArticulo(Gimnasio $gimnasio, Articulo $articulo)
    {
        $articulo->delete();

        return response()->json();
    }
}

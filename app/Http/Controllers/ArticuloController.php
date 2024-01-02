<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticuloCrearArticuloRequest;
use App\Http\Requests\ArticuloEditarArticuloRequest;
use App\Http\Requests\ArticuloPagarCompraRequest;
use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\UsuarioCompraArticulo;
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

    public function historialDeCompras(Gimnasio $gimnasio)
    {
        $compras = auth()->user()->historialDeCompras()->wherePivot("gimnasio", $gimnasio->id)->get();

        return response()->json($compras);
    }

    public function comprarArticulo(Gimnasio $gimnasio, Articulo $articulo)
    {
        $user = auth()->user();
        $user->historialDeCompras()->attach($articulo, ["gimnasio" => $gimnasio->id]);

        //Decrementamos stock
        $articulo->stock--;
        $articulo->save();

        return response()->json();
    }

    public function pagarCompra(Gimnasio $gimnasio, UsuarioCompraArticulo $compra, ArticuloPagarCompraRequest $request)
    {
        $compra->pagada = now();
        $compra->save();

        return response()->json();
    }
}

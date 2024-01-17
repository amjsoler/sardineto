<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComprobarSiLaPeticionSeHaceComoInvitado
{
    public function handle(Request $request, Closure $next): Response
    {
       if(auth()->check()) {
           return response()->json("Ya estás autenticado", 461);
       }else{
            return $next($request);
       }
    }
}

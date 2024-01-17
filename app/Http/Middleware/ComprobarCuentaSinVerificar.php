<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComprobarCuentaSinVerificar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!isset(auth()->user()->email_verified_at)){
            return $next($request);
        }else{
            return response()->json("Cuenta ya verificada", 463);
        }
    }
}

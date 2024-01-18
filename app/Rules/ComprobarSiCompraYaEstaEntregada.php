<?php

namespace App\Rules;

use App\Models\UsuarioCompraArticulo;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiCompraYaEstaEntregada implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(UsuarioCompraArticulo::find($value)->entregada !== null)
        {
            $fail(__("validation.articulo.compra.ComprobarSiCompraYaEstaEntregada"));
        }
    }
}

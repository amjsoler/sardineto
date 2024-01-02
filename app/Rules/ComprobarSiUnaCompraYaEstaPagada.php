<?php

namespace App\Rules;

use App\Models\UsuarioCompraArticulo;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiUnaCompraYaEstaPagada implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(UsuarioCompraArticulo::find($value)->pagada !== null)
        {
            $fail(__("validation.articulo.compra.ComprobarSiUnaCompraYaEstaPagada"));
        }
    }
}

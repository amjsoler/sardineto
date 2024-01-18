<?php

namespace App\Rules;

use App\Models\Clase;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiQuedanPlazasEnLaClase implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $clase = Clase::find($value);

        if($clase->participantes()->count() >= $clase->plazas)
        {
            $fail(__("validation.clase.claseId.ComprobarSiQuedanPlazasEnLaClase"));
        }
    }
}

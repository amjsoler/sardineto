<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ContrasenaActualCorrectaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //Si no se ha encontrado el usuario...
        if(!Hash::check($value, auth()->user()->password)){
            $fail(__("validation.usuario.contrasenaActual.ContrasenaActualCorrectaRule"));
        }
    }
}

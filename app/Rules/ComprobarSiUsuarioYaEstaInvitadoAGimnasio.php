<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiUsuarioYaEstaInvitadoAGimnasio implements ValidationRule
{
    public function __construct(public int $gimnasio){}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $usuario = User::where("email", $value)->first();

        if(isset($usuario) && $usuario->gimnasiosInvitado()->wherePivot("gimnasio", $this->gimnasio)->count() > 0){
            $fail(__("validation.gimnasio.email.comprobarSiUsuarioYaEstaInvitadoAGimnasio"));
        }
    }
}

<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiUsuarioYaEstaInvitadoAGimnasio implements ValidationRule
{
    public function __construct(public $gimnasio)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $usuario = User::where("email", $value)->first();

        if($usuario->gimnasiosInvitado()->wherePivot("gimnasio", $this->gimnasio)->count() > 0){
            $fail(__("validation.gimnasio.email.comprobarSiUsuarioYaEstaInvitadoAGimnasio"));
        }
    }
}

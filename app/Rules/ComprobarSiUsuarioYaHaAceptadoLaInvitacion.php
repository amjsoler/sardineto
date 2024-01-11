<?php

namespace App\Rules;

use App\Models\Gimnasio;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiUsuarioYaHaAceptadoLaInvitacion implements ValidationRule
{
    public function __construct(public Gimnasio $gimnasio, public User $usuario){}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $usuarioGimnasio = $this->usuario->gimnasiosInvitado()
            ->wherePivot("gimnasio", $this->gimnasio->id)
            ->withPivot("invitacion_aceptada")
            ->first();

        if(isset($usuarioGimnasio) && $usuarioGimnasio->pivot->invitacion_aceptada){
            $fail(__("validation.gimnasio.usuarioId.yaAceptado"));
        }
    }
}

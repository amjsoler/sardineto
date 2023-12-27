<?php

namespace App\Rules;

use App\Models\Gimnasio;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiUsuarioYaHaAceptadoLaInvitacion implements ValidationRule
{
    public function __construct(public Gimnasio $gimnasio, public User $usuario)
    {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->usuario->gimnasiosInvitado()->wherePivot("gimnasio", $this->gimnasio->id)->withPivot("invitacion_aceptada")->first()->pivot->invitacion_aceptada){
            $fail(__("validation.gimnasio.usuarioId.yaAceptado"));
        }
    }
}

<?php

namespace App\Rules;

use App\Helpers\Helpers;
use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ComprobarSiUserReuneRequisitosParaApuntarseAClase implements ValidationRule
{
    public $gimnasioId;

    public function __construct($gimnasioId)
    {
        $this->gimnasioId = $gimnasioId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(Helpers::dameSuscripcionActivaOAbonoDeUsuario(User::find($value), Gimnasio::find($this->gimnasioId)) === null){
            $fail(__("validation.clase.usuarioId.ComprobarSiUserReuneRequisitosParaApuntarseAClase"));
        }
    }
}

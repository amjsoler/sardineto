<?php

namespace App\Policies;

use App\Models\Metrica;
use App\Models\User;

class MetricaPolicy
{
    public function eliminarMetricas(User $usuario, Metrica $metrica)
    {
        return $metrica->usuario === $usuario->id;
    }
}

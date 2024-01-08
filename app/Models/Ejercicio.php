<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ejercicio extends Model
{
    use HasFactory;

    protected $table = "ejercicios";
    protected $fillable = ["nombre", "descripcion", "demostracion"];

    public function gimnasioPropietario() : BelongsTo
    {
        return $this->belongsTo(Gimnasio::class, "gimnasio", "id");
    }

    public function clases(): BelongsToMany
    {
        return $this->belongsToMany(Clase::class, "ejercicios_clases", "ejercicio", "clase", "id", "id");
    }

    public function registrosPeso(): HasMany
    {
        return $this->hasMany(EjercicioUsuario::class, "ejercicio", "id");
    }
}

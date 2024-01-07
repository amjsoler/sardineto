<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}

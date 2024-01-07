<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ejercicio extends Model
{
    use HasFactory;

    protected $table = "ejercicios";
    protected $fillable = ["nombre", "descripcion", "demostracion"];

    public function gimnasioPropietario() : BelongsTo
    {
        return $this->belongsTo(Gimnasio::class, "gimnasio", "id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ejercicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "ejercicios";

    protected $fillable = [
        "nombre",
        "descripcion",
        "demostracion"
    ];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function ejerciciosUsuarios() : HasMany
    {
        return $this->hasMany(EjercicioUsuario::class, "ejercicio", "id");
    }
}

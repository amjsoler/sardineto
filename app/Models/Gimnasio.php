<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gimnasio extends Model
{
    use HasFactory;

    protected $fillable = ["nombre", "descripcion", "logo", "direccion"];

    public function clases(): HasMany
    {
        return $this->hasMany(Clase::class, "gimnasio", "id");
    }

    public function usuariosInvitados() : BelongsToMany
    {
        return $this->belongsToMany(User::class,
            "usuarios_gimnasios",
            "gimnasio",
            "usuario",
            "id",
            "id")->withTimestamps();
    }

    public function tarifas() : HasMany
    {
        return $this->hasMany(Tarifa::class, "gimnasio", "id");
    }
}

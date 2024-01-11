<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gimnasio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "nombre",
        "descripcion",
        "logo",
        "direccion"
    ];

    protected $hidden = [
        "propietario",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

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

    public function suscripciones(): HasMany
    {
        return $this->hasMany(Suscripcion::class, "gimnasio", "id");
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class, "gimnasio", "id");
    }

    public function ejercicios() : HasMany
    {
        return $this->hasMany(Ejercicio::class, "gimnasio", "id");
    }

    public function administradores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "administradores", "gimnasio", "usuario", "id", "id")->withTimestamps();
    }
}

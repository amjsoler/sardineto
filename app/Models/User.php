<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'password',
        "alertasporcorreo",
        "alertaspornotificacion"
    ];

    protected $hidden = [
        'password',
        'remember_token',
        "email_verified_at",
        "created_at",
        "updated_at"
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function gimnasiosPropietario(): HasMany
    {
        return $this->hasMany(Gimnasio::class, "propietario", "id");
    }

    public function gimnasiosInvitado() : BelongsToMany
    {
        return $this->belongsToMany(Gimnasio::class, "usuarios_gimnasios", "usuario", "gimnasio", "id", "id");
    }

    public function historialDeCompras() : BelongsToMany
    {
        return $this->belongsToMany(Articulo::class, "usuarios_compran_articulos", "usuario", "articulo", "id", "id")->withTimestamps();
    }

    public function metricas() : HasMany
    {
        return $this->hasMany(Metrica::class, "usuario", "id");
    }

    public function registrosPeso(): HasMany
    {
        return $this->hasMany(EjercicioUsuario::class, "usuario", "id");
    }

    public function clasesEnLasQueParticipa(): BelongsToMany
    {
        return $this->belongsToMany(Clase::class, "usuarios_participan_clases", "usuario", "clase", "id", "id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gimnasio extends Model
{
    use HasFactory;

    protected $fillable = ["nombre", "descripcion", "logo", "direccion"];

    public function usuariosInvitados() : BelongsToMany
    {
        return $this->belongsToMany(User::class,
            "usuarios_gimnasios",
            "gimnasio",
            "usuario",
            "id",
            "id")->withTimestamps();
    }
}

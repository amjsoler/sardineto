<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Clase extends Model
{
    use HasFactory;


    protected $fillable = ["nombre", "descripcion", "fechayhora", "plazas"];
    public function gimnasioPerteneciente(): BelongsTo
    {
        return $this->belongsTo(Gimnasio::class, "gimnasio", "id");
    }

    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "usuarios_participan_clases", "clase", "usuario", "id", "id")->withTimestamps();
    }
}

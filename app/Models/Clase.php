<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "clases";

    protected $fillable = [
        "nombre",
        "descripcion",
        "fechayhora",
        "plazas"
    ];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "usuarios_participan_clases", "clase", "usuario", "id", "id")->withTimestamps();
    }

    public function ejercicios(): BelongsToMany
    {
        return $this->belongsToMany(Ejercicio::class, "ejercicios_clases", "clase", "ejercicio", "id", "id")->withPivot("detalles");
    }
}

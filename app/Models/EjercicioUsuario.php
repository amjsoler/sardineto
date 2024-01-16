<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjercicioUsuario extends Model
{
    use HasFactory;

    protected $table = "ejercicios_usuarios";

    protected $fillable = ["unorm"];

    protected $hidden = ["created_at", "updated_at"];

    public function ejercicio() : BelongsTo
    {
        return $this->belongsTo(Ejercicio::class, "ejercicio", "id");
    }

    public function usuario() : BelongsTo
    {
        return $this->belongsTo(User::class, "usuario", "id");
    }
}

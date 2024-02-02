<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UsuarioCompraArticulo extends Model
{
    use HasFactory;

    protected $table = "usuarios_compran_articulos";

    public function articulo() : BelongsTo
    {
        return $this->belongsTo(Articulo::class, "articulo", "id");
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, "usuario", "id");
    }
}

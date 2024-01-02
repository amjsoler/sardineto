<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Articulo extends Model
{
    use HasFactory;

    protected $table = "articulos";

    protected $guarded = ["gimnasio"];

    public function gimnasioAlQuePertenece(): BelongsTo
    {
        return $this->belongsTo(Gimnasio::class, "gimnasio", "id");
    }

    public function historialDeCompras(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "usuarios_compran_articulos", "articulo", "usuario", "id", "id");
    }
}

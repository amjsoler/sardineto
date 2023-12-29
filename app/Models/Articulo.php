<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Articulo extends Model
{
    use HasFactory;

    protected $table = "articulos";

    protected $guarded = ["gimnasio"];

    public function gimnasioAlQuePertenece(): BelongsTo
    {
        return $this->belongsTo(Gimnasio::class, "gimnasio", "id");
    }
}

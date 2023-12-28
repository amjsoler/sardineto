<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suscripcion extends Model
{
    use HasFactory;

    protected $fillable = ["tarifa", "usuario"];

    protected $table = "suscripciones";

    public function usuarioSuscriptor(): BelongsTo
    {
        return $this->belongsTo(User::class, "usuario", "id");
    }

    public function tarifaSuscrita(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class, "tarifa", "id");
    }

    public function gimnasioAlQuePertenece(): BelongsTo
    {
        return $this->belongsTo(Gimnasio::class, "gimnasio", "id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarifa extends Model
{
    use HasFactory;

    protected $fillable = ["nombre", "precio", "creditos"];

    public function gimnasioAlQuePertenece() : BelongsTo
    {
        return $this->belongsTo(Gimnasio::class, "gimnasio", "id");
    }
}

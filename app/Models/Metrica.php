<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Metrica extends Model
{
    use HasFactory;

    protected $table = "metricas";

    protected $guarded = [];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, "usuario", "id");
    }
}

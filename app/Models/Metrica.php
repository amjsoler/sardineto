<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metrica extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "metricas";

    protected $fillable = [
        "peso",
        "porcentaje_graso"
    ];
    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, "usuario", "id");
    }
}

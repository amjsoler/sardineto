<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suscripcion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "suscripciones";

    protected $fillable = ["tarifa"];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function tarifaALaQuePertenece() : BelongsTo
    {
        return $this->belongsTo(Tarifa::class, "tarifa", "id")->withTrashed();
    }

    public function usuarioQueSeSuscribe() : belongsTo
    {
        return $this->belongsTo(User::class, "usuario", "id")->withTrashed();
    }
}

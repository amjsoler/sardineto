<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecuperarCuentaToken extends Model
{
    use HasFactory;

    protected $table = "recuperar_cuenta_tokens";

    protected $fillable = ["usuario", "token", "valido_hasta"];
}

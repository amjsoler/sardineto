<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountVerifyToken extends Model
{
    use HasFactory;

    protected $table = "account_verify_tokens";

    protected $fillable = ["usuario", "token", "valido_hasta"];
}

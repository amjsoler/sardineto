<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function esAdmin(User $user): bool
    {
        if($user->email == env("ADMIN_AUTORIZADO")){
            return true;
        }else{
            return false;
        }
    }
}

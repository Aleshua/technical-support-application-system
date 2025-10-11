<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function me(User $authUser, User $user): bool
    {
        if ($authUser->role === 'admin') {
            return true;
        }

        return $authUser->id === $user->id;
    }

    public function admin(User $authUser, User $user): bool
    {
        return $authUser->role === 'admin';
    }
}

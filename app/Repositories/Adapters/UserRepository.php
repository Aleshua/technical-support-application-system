<?php

namespace App\Repositories\Adapters;

use App\Models\User;

use App\Repositories\Ports\IUserRepository;

class UserRepository implements IUserRepository
{
    public function save(array $data): User
    {
        return User::create($data);
    }
}

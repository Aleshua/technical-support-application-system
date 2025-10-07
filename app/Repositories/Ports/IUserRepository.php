<?php

namespace App\Repositories\Ports;

use App\Models\User;

interface IUserRepository
{
    public function save(array $data): User;
}
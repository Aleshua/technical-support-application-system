<?php

namespace App\Repositories\Ports;

use App\Models\User;

interface IUserRepository
{
    public function findById(int $id): User;
    public function save(array $data): User;
    public function update(int $idUser, array $data): void;
    public function delete(int $idUser): void;
}

<?php

namespace App\UseCases\Ports;

use App\Models\User;

interface IUserUseCases
{
    public function findUser(int $id): User;
    public function updateUser(int $idUser, array $data): void;
    public function deleteUser(int $idUser): void;
}

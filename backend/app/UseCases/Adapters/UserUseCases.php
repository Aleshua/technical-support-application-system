<?php

namespace App\UseCases\Adapters;

use App\Models\User;

use App\UseCases\Ports\IUserUseCases;

use App\Repositories\Ports\IUserRepository;

class UserUseCases implements IUserUseCases
{
    protected IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findUser(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    public function updateUser(int $idUser, array $data): void
    {
        $this->userRepository->update($idUser, $data);
    }

    public function deleteUser(int $idUser): void
    {
        $this->userRepository->delete($idUser);
    }
}

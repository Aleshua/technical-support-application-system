<?php

namespace App\Repositories\Adapters;

use App\Models\User;

use App\Repositories\Ports\IUserRepository;

use Illuminate\Support\Facades\Gate;

class UserRepository implements IUserRepository
{
    public function save(array $data): User
    {
        return User::create($data);
    }

    public function findById(int $id): User
    {
        $user = User::findOrFail($id);

        Gate::authorize('me', $user);

        return $user;
    }

    public function update(int $idUser, array $data): void
    {
        $user = User::findOrFail($idUser);

        Gate::authorize('me', $user);
        
        if (isset($data['role'])) {
            Gate::authorize('admin', $user);
        }
        
        $user->update($data);
    }

    public function delete(int $idUser): void
    {
        $user = User::findOrFail($idUser);

        Gate::authorize('me', $user);

        $user->delete();
    }
}

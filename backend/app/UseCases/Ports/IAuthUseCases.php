<?php

namespace App\UseCases\Ports;

use App\Models\User;

interface IAuthUseCases
{
    public function register(array $data): void;
    public function login(array $credentials): User;
    public function logout(): void;
    public function resendEmailVerification(): void;
}

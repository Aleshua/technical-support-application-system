<?php

namespace App\Services\Ports;

use App\Models\User;

interface IEmailService
{
    public function sendVerification(User $user): void;
}

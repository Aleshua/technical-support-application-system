<?php

namespace App\Services\Adapters;

use App\Models\User;

use App\Services\Ports\IEmailService;

class EmailService implements IEmailService
{
    public function sendVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }
}

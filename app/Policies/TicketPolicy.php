<?php

namespace App\Policies;

use Log;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function related(User $authUser, Ticket $ticket): bool
    {
        if ($authUser->role === 'admin') {
            return true;
        }

        if ($authUser->role === 'operator') {
            return ($ticket->executor_id == $authUser->id) || ($ticket->status === 'new');
        }

        if ($authUser->role === 'user') {
            return $ticket->customer_id == $authUser->id;
        }

        return false;
    }

    public function save(User $authUser, Ticket $ticket): bool
    {
        if ($authUser->role === 'admin' || $authUser->role === 'user') {
            return true;
        }

        return false;
    }

    public function me(User $authUser, int $userId): bool
    {
        if ($authUser->role === 'admin') {
            return true;
        }

        return $authUser->id == $userId;
    }

    public function operator(User $authUser): bool
    {
        if ($authUser->role === 'admin') {
            return true;
        }

        if ($authUser->role === 'operator') {
            return true;
        }

        return false;
    }
}

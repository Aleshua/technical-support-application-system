<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use App\Models\User;
use App\Models\Ticket;

use App\Policies\UserPolicy;
use App\Policies\TicketPolicy;

use App\Repositories\Ports\IUserRepository;
use App\Repositories\Ports\ITicketRepository;
use App\Repositories\Ports\ITicketTypeRepository;

use App\Repositories\Adapters\UserRepository;
use App\Repositories\Adapters\TicketRepository;
use App\Repositories\Adapters\TicketTypeRepository;

use App\UseCases\Ports\IAuthUseCases;
use App\UseCases\Ports\IUserUseCases;
use App\UseCases\Ports\ITicketUseCases;

use App\UseCases\Adapters\AuthUseCases;
use App\UseCases\Adapters\UserUseCases;
use App\UseCases\Adapters\TicketUseCases;

use App\Services\Ports\IEmailService;

use App\Services\Adapters\EmailService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IUserRepository::class, UserRepository::class);
        $this->app->singleton(ITicketRepository::class, TicketRepository::class);
        $this->app->singleton(ITicketTypeRepository::class, TicketTypeRepository::class);

        $this->app->singleton(IAuthUseCases::class, AuthUseCases::class);
        $this->app->singleton(IUserUseCases::class, UserUseCases::class);
        $this->app->singleton(ITicketUseCases::class, TicketUseCases::class);

        $this->app->singleton(IEmailService::class, EmailService::class);
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
    }
}

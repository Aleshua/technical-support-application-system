<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use App\Models\User;
use App\Policies\UserPolicy;

use App\Repositories\Ports\IUserRepository;

use App\Repositories\Adapters\UserRepository;

use App\UseCases\Ports\IAuthUseCases;
use App\UseCases\Ports\IUserUseCases;

use App\UseCases\Adapters\AuthUseCases;
use App\UseCases\Adapters\UserUseCases;

use App\Services\Ports\IEmailService;

use App\Services\Adapters\EmailService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IUserRepository::class, UserRepository::class);

        $this->app->singleton(IAuthUseCases::class, AuthUseCases::class);
        $this->app->singleton(IUserUseCases::class, UserUseCases::class);

        $this->app->singleton(IEmailService::class, EmailService::class);
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
    }
}

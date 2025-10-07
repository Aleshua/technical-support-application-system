<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Ports\IUserRepository;

use App\Repositories\Adapters\UserRepository;

use App\UseCases\Ports\IAuthUseCases;

use App\UseCases\Adapters\AuthUseCases;

use App\Services\Ports\IEmailService;

use App\Services\Adapters\EmailService;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(IUserRepository::class, UserRepository::class);

        $this->app->singleton(IAuthUseCases::class, AuthUseCases::class);

        $this->app->singleton(IEmailService::class, EmailService::class);
    }

    public function boot(): void
    {

    }
}

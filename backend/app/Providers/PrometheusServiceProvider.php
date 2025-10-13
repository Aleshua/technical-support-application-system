<?php

namespace App\Providers;

use Exception;

use App\Models\User;
use App\Models\Ticket;

use Prometheus\Storage\Redis;
use Prometheus\CollectorRegistry;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PrometheusServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CollectorRegistry::class, function () {
            $redisConfig = Config::get('database.redis.default');

            return new CollectorRegistry(new Redis([
                'host' => $redisConfig['host'] ?? '127.0.0.1',
                'port' => $redisConfig['port'] ?? 6379,
                'password' => $redisConfig['password'] ?? null,
                'database' => $redisConfig['database'] ?? 0,
            ]));
        });
    }

    public function boot(CollectorRegistry $registry)
    {
        try {
        $registry->getOrRegisterGauge(
            'app',
            'users_total',
            'Total users'
        )->set(User::count());

        $registry->getOrRegisterGauge(
            'app',
            'tickets_total',
            'Total tickets'
        )->set(Ticket::count());

        $registry->getOrRegisterGauge(
            'app',
            'tickets_open',
            'Open tickets'
        )->set(Ticket::where('status', 'open')->count());

        $registry->getOrRegisterGauge(
            'app',
            'tickets_closed',
            'Closed tickets'
        )->set(Ticket::where('status', 'closed')->count());
        } catch (Exception $e) {}
    }
}

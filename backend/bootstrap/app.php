<?php

use App\Http\ApiExceptionHandler;

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\PrometheusMetricsMiddleware;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/auth.php',
            __DIR__ . '/../routes/user.php',
            __DIR__ . '/../routes/ticket.php',
            __DIR__ . '/../routes/metrics.php',
        ],
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
        $middleware->append(PrometheusMetricsMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        ApiExceptionHandler::processing($exceptions);
    })->create();

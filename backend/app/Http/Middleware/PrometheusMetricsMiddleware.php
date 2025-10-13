<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Prometheus\CollectorRegistry;

class PrometheusMetricsMiddleware
{
    protected CollectorRegistry $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        $route = Route::currentRouteName() ?? $request->path();
        $method = $request->method();
        $status = $response->status();

        $counter = $this->registry->getOrRegisterCounter(
            'app',
            'http_requests_total',
            'Total HTTP requests',
            ['route', 'method', 'status'],
        );
        $counter->inc(['route' => $route, 'method' => $method, 'status' => (string) $status]);

        $histogram = $this->registry->getOrRegisterHistogram(
            'app',
            'http_request_duration_seconds',
            'HTTP request duration',
            ['route', 'method', 'status'],
            [0.1, 0.5, 1, 2, 5],
        );
        $histogram->observe(
            $duration,
            ['route' => $route, 'method' => $method, 'status' => (string) $status],
        );


        return $response;
    }
}
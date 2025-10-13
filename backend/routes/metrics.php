<?php

use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

Route::get('/metrics', function (CollectorRegistry $registry) {
    $renderer = new RenderTextFormat();
    $metrics = $renderer->render($registry->getMetricFamilySamples());

    return response($metrics, 200)
        ->header('Content-Type', RenderTextFormat::MIME_TYPE);
});
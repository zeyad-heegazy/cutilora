<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/internal/deploy', function () {
    $clearExitCode = Artisan::call('optimize:clear');
    $clearOutput = Artisan::output();

    $routes = collect(app('router')->getRoutes());

    $loginRoutes = $routes
        ->filter(fn ($route) => str_contains($route->uri(), 'auth/platform/login'))
        ->map(fn ($route) => [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'action' => $route->getActionName(),
        ])
        ->values()
        ->all();

    $apiRoutesCount = $routes
        ->filter(fn ($route) => str_starts_with($route->uri(), 'v1/'))
        ->count();

    return response()->json([
        'success' => $clearExitCode === 0,
        'message' => 'Cache clear and route diagnostics completed.',
        'cache_clear_output' => $clearOutput,
        'platform_login_routes' => $loginRoutes,
        'registered_v1_api_routes_count' => $apiRoutesCount,
        'login_route_registered' => ! empty($loginRoutes),
    ], $clearExitCode === 0 ? 200 : 500);
});

<?php

use Apiato\Foundation\Apiato;
use Apiato\Foundation\Configuration\Routing;
use Apiato\Http\Middleware\ProcessETag;
use Apiato\Http\Middleware\ValidateJsonContent;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\HomePageController;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\LoginController;
use App\Ship\Middleware\EnsurePlatformAdmin;
use App\Ship\Middleware\ValidateAppId;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

$basePath = dirname(__DIR__);
$apiato = Apiato::configure(basePath: $basePath)->create();

$apiato->withRouting(function (Routing $routing): void {
    $routing->resolveApiVersionUsing(
        static fn (string $file): string => Str::of(basename($file))
            ->before('.php')
            ->betweenFirst('.', '.')
            ->value(),
    );
});

return Application::configure(basePath: $basePath)
    ->withProviders($apiato->providers())
    ->withEvents($apiato->events())
    ->withRouting(
        web: $apiato->webRoutes(),
        channels: __DIR__ . '/../app/Ship/Broadcasting/channels.php',
        health: '/up',
        then: static fn() => $apiato->registerApiRoutes(),
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'platform-admin' => EnsurePlatformAdmin::class,
        ]);
        $middleware->use([
            ValidateAppId::class,
        ]);
        $middleware->api(append: [
            ValidateJsonContent::class,
            ProcessETag::class,
        ]);
        $middleware->redirectUsersTo(static function (Request $request): string {
            return action(HomePageController::class);
        });
        $middleware->redirectGuestsTo(static function (Request $request): string {
            return action([LoginController::class, 'showForm']);
        });
    })
    ->withCommands($apiato->commands())
    ->withExceptions(static function (Exceptions $exceptions) { })
    ->create();

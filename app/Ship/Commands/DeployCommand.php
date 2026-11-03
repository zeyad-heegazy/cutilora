<?php

namespace App\Ship\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DeployCommand extends Command
{
    protected $signature = 'cutilora:deploy';

    protected $description = 'Clear caches and diagnose API route registration';

    public function handle(): int
    {
        $this->components->info('Clearing Laravel caches...');

        Artisan::call('optimize:clear');
        $this->output->write(Artisan::output());

        $this->components->info('Checking API route registration...');

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

        $this->line('Platform login routes:');
        $this->line(json_encode($loginRoutes, JSON_PRETTY_PRINT));
        $this->line('Registered v1 API routes count: ' . $apiRoutesCount);

        if (empty($loginRoutes)) {
            $this->components->error('Platform login route is NOT registered.');
        } else {
            $this->components->success('Platform login route is registered.');
        }

        $this->components->success('Deployment diagnostics completed.');

        return self::SUCCESS;
    }
}

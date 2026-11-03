<?php

namespace App\Ship\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DeployCommand extends Command
{
    protected $signature = 'cutilora:deploy';

    protected $description = 'Run Cutilora deployment tasks';

    public function handle(): int
    {
        $this->components->info('Clearing Laravel caches...');

        Artisan::call('optimize:clear');
        $this->output->write(Artisan::output());

        $this->components->info('Checking API route registration...');

        $loginRoutes = collect(app('router')->getRoutes())
            ->filter(fn ($route) => str_contains($route->uri(), 'auth/platform/login'))
            ->map(fn ($route) => [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'action' => $route->getActionName(),
            ])
            ->values()
            ->all();

        $apiRoutes = collect(app('router')->getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'v1/'))
            ->map(fn ($route) => $route->uri())
            ->values()
            ->all();

        $this->line('Platform login routes:');
        $this->line(json_encode($loginRoutes, JSON_PRETTY_PRINT));

        $this->line('Registered v1 API routes count: ' . count($apiRoutes));

        if (empty($loginRoutes)) {
            $this->components->error('Platform login route is NOT registered.');
        } else {
            $this->components->success('Platform login route is registered.');
        }

        $commands = [
            'Migrations' => [
                'migrate',
                ['--force' => true],
            ],

            'First Platform Admin Seeder' => [
                'db:seed',
                [
                    '--class' => 'App\\Containers\\AppSection\\User\\Data\\Seeders\\FirstPlatformAdminSeeder',
                    '--force' => true,
                ],
            ],

            'Geo Starter Seeder' => [
                'db:seed',
                [
                    '--class' => 'App\\Containers\\Geo\\Admin\\Data\\Seeders\\GeoStarterSeeder',
                    '--force' => true,
                ],
            ],
        ];

        foreach ($commands as $name => [$command, $arguments]) {
            $this->components->info("Running: {$name}");

            $exitCode = Artisan::call($command, $arguments);

            $this->output->write(Artisan::output());

            if ($exitCode !== self::SUCCESS) {
                $this->components->error("Failed: {$name}");

                return self::FAILURE;
            }

            $this->components->success("Completed: {$name}");
        }

        $this->components->success('Cutilora deployment completed successfully.');

        return self::SUCCESS;
    }
}

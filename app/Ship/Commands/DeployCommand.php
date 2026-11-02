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
        $commands = [
            'Migrations' => [
                'migrate',
                ['--force' => true],
            ],

            'First Platform Admin Seeder' => [
                'db:seed',
                [
                    '--class' => 'App\Containers\AppSection\User\Data\Seeders\FirstPlatformAdminSeeder',
                    '--force' => true,
                ],
            ],

            'Geo Starter Seeder' => [
                'db:seed',
                [
                    '--class' => 'App\Containers\Geo\Admin\Data\Seeders\GeoStarterSeeder',
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
<?php

namespace App\Containers\Tenancy\Clinic\Data\Seeders;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Seeders\Seeder as ParentSeeder;

final class ClinicDemoSeeder extends ParentSeeder
{
    public function run(): void
    {
        Clinic::query()->firstOrCreate(
            ['name' => 'Cutilora Demo Clinic'],
            [
                'country' => 'EG',
                'timezone' => 'Africa/Cairo',
                'currency' => 'EGP',
                'subscription_status' => Clinic::STATUS_TRIAL,
            ],
        );
    }
}

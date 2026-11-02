<?php

namespace App\Containers\Tenancy\Clinic\Tasks;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class CreateClinicTask extends ParentTask
{
    public function run(array $data): Clinic
    {
        return Clinic::query()->create([
            'name' => $data['name'],
            'country_id' => $data['country_id'] ?? null,
            'governorate_id' => $data['governorate_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'timezone' => $data['timezone'],
            'currency' => strtoupper($data['currency']),
            'subscription_status' => Clinic::STATUS_TRIAL,
            'plan_id' => $data['plan_id'] ?? null,
        ])->load(['country', 'governorate', 'city', 'district']);
    }
}

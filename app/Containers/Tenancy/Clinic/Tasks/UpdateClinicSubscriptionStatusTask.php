<?php

namespace App\Containers\Tenancy\Clinic\Tasks;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateClinicSubscriptionStatusTask extends ParentTask
{
    public function run(int $clinicId, string $status): Clinic
    {
        $clinic = Clinic::query()->lockForUpdate()->findOrFail($clinicId);
        $clinic->subscription_status = $status;
        $clinic->save();

        return $clinic->load(['country', 'governorate', 'city', 'district']);
    }
}

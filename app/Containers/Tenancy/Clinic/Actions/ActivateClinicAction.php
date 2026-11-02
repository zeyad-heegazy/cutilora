<?php

namespace App\Containers\Tenancy\Clinic\Actions;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Containers\Tenancy\Clinic\Tasks\UpdateClinicSubscriptionStatusTask;
use App\Containers\Tenancy\Clinic\Tasks\WriteClinicAuditLogTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

final class ActivateClinicAction extends ParentAction
{
    public function __construct(
        private readonly UpdateClinicSubscriptionStatusTask $updateClinicSubscriptionStatusTask,
        private readonly WriteClinicAuditLogTask $writeClinicAuditLogTask,
    ) {
    }

    public function run(int $clinicId, int $actorId, string|null $reason = null): Clinic
    {
        return DB::transaction(function () use ($clinicId, $actorId, $reason): Clinic {
            $clinic = $this->updateClinicSubscriptionStatusTask->run($clinicId, Clinic::STATUS_ACTIVE);
            $this->writeClinicAuditLogTask->run($clinic->id, $actorId, 'clinic.activated', $reason);

            return $clinic;
        });
    }
}

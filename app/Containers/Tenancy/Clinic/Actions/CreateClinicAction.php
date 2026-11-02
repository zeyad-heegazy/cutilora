<?php

namespace App\Containers\Tenancy\Clinic\Actions;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Containers\Tenancy\Clinic\Tasks\CreateClinicTask;
use App\Containers\Tenancy\Clinic\Tasks\WriteClinicAuditLogTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

final class CreateClinicAction extends ParentAction
{
    public function __construct(
        private readonly CreateClinicTask $createClinicTask,
        private readonly WriteClinicAuditLogTask $writeClinicAuditLogTask,
    ) {
    }

    public function run(array $data, int $actorId): Clinic
    {
        return DB::transaction(function () use ($data, $actorId): Clinic {
            $clinic = $this->createClinicTask->run($data);
            $this->writeClinicAuditLogTask->run($clinic->id, $actorId, 'clinic.created');

            return $clinic;
        });
    }
}

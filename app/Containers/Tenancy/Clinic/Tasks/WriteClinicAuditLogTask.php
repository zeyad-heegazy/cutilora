<?php

namespace App\Containers\Tenancy\Clinic\Tasks;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\DB;

final class WriteClinicAuditLogTask extends ParentTask
{
    public function run(int $clinicId, int $actorId, string $action, string|null $reason = null): void
    {
        DB::table('audit_logs')->insert([
            'clinic_id' => $clinicId,
            'actor_type' => 'platform_admin',
            'actor_id' => $actorId,
            'action' => $action,
            'subject_type' => Clinic::class,
            'subject_id' => $clinicId,
            'meta' => null,
            'reason' => $reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

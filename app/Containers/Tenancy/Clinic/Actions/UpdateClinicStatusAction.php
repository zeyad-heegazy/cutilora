<?php

namespace App\Containers\Tenancy\Clinic\Actions;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Validation\ValidationException;

final class UpdateClinicStatusAction extends ParentAction
{
    public function __construct(
        private readonly ActivateClinicAction $activateClinicAction,
        private readonly SuspendClinicAction $suspendClinicAction,
    ) {
    }

    public function run(int $clinicId, int $actorId, string $status, string|null $reason = null): Clinic
    {
        if (Clinic::STATUS_SUSPENDED === $status) {
            if (is_null($reason) || mb_strlen(trim($reason)) < 3) {
                throw ValidationException::withMessages([
                    'reason' => ['A valid reason is required to suspend a clinic.'],
                ]);
            }

            return $this->suspendClinicAction->run($clinicId, $actorId, $reason);
        }

        if (Clinic::STATUS_ACTIVE === $status) {
            return $this->activateClinicAction->run($clinicId, $actorId, $reason);
        }

        throw ValidationException::withMessages([
            'subscription_status' => ['The selected subscription status is invalid.'],
        ]);
    }
}

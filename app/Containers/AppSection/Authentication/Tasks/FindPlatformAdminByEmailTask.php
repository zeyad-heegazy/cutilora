<?php

namespace App\Containers\AppSection\Authentication\Tasks;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class FindPlatformAdminByEmailTask extends ParentTask
{
    public function run(string $email): User|null
    {
        return User::query()
            ->whereRaw('LOWER(email) = LOWER(?)', [$email])
            ->where('is_platform_admin', true)
            ->first();
    }
}

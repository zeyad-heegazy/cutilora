<?php

namespace App\Containers\AppSection\User\Data\Seeders;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Seeders\Seeder as ParentSeeder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class FirstPlatformAdminSeeder extends ParentSeeder
{
    public function run(): void
    {
        $email = config('platform.first_admin.email');
        $password = config('platform.first_admin.password');

        if (blank($email) && blank($password)) {
            return;
        }

        if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('PLATFORM_ADMIN_EMAIL must be a valid email address.');
        }

        $name = config('platform.first_admin.name', 'Platform Admin');

        DB::transaction(function () use ($email, $name, $password): void {
            $user = User::query()
                ->whereRaw('LOWER(email) = LOWER(?)', [$email])
                ->lockForUpdate()
                ->first();

            if ($user) {
                if (!$user->is_platform_admin) {
                    $user->is_platform_admin = true;
                    $user->save();
                }

                return;
            }

            if (!is_string($password) || strlen($password) < 6) {
                throw new InvalidArgumentException(
                    'PLATFORM_ADMIN_PASSWORD must be at least 6 characters when creating the first platform admin.',
                );
            }

            $user = User::query()->create([
                'name' => is_string($name) && '' !== trim($name) ? $name : 'Platform Admin',
                'email' => $email,
                'password' => $password,
            ]);
            $user->is_platform_admin = true;
            $user->save();
        });
    }
}

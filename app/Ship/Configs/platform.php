<?php

return [
    'first_admin' => [
        'email' => env('PLATFORM_ADMIN_EMAIL'),
        'name' => env('PLATFORM_ADMIN_NAME', 'Platform Admin'),
        'password' => env('PLATFORM_ADMIN_PASSWORD'),
    ],
];

<?php

use App\Containers\AppSection\Authentication\UI\API\Controllers\PlatformLoginController;
use Illuminate\Support\Facades\Route;

Route::post('auth/platform/login', PlatformLoginController::class);

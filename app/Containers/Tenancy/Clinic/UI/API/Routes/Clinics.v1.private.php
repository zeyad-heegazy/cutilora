<?php

use App\Containers\Tenancy\Clinic\UI\API\Controllers\ClinicController;
use Illuminate\Support\Facades\Route;

Route::get('platform/clinics', [ClinicController::class, 'index'])
    ->middleware(['auth:api', 'platform-admin']);

Route::post('platform/clinics', [ClinicController::class, 'store'])
    ->middleware(['auth:api', 'platform-admin']);

Route::patch('platform/clinics/{clinic}/status', [ClinicController::class, 'updateStatus'])
    ->middleware(['auth:api', 'platform-admin']);

<?php

use App\Containers\Geo\Admin\UI\API\Controllers\GeoAdminController;
use Illuminate\Support\Facades\Route;

$resources = ['countries', 'governorates', 'cities', 'districts'];

Route::middleware(['auth:api', 'platform-admin'])
    ->prefix('platform/geo/{resource}')
    ->whereIn('resource', $resources)
    ->group(static function (): void {
        Route::get('/', [GeoAdminController::class, 'index']);
        Route::post('/', [GeoAdminController::class, 'store']);
        Route::get('/{entry}', [GeoAdminController::class, 'show']);
        Route::patch('/{entry}', [GeoAdminController::class, 'update']);
        Route::delete('/{entry}', [GeoAdminController::class, 'destroy']);
    });

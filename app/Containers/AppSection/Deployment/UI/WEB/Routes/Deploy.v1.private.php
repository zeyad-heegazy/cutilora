<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/internal/deploy', function () {

    $exitCode = Artisan::call('cutilora:deploy');

    $output = Artisan::output();

    return response()->json([
        'success' => $exitCode === 0,
        'message' => $exitCode === 0
            ? 'Deployment completed successfully.'
            : 'Deployment failed.',
        'output' => $output,
    ], $exitCode === 0 ? 200 : 500);

});
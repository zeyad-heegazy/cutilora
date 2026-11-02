<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/internal/deploy', function () {

    $exitCode = Artisan::call('cutilora:deploy');

    $output = Artisan::output();

    if ($exitCode !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'Deployment failed.',
            'output' => $output,
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Deployment completed successfully.',
        'output' => $output,
    ]);

});
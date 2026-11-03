<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    // Development configuration: accept requests from any origin.
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Wildcard origins cannot be used with credentialed browser requests.
    'supports_credentials' => false,

];

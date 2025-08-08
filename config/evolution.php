<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Evolution API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for Evolution API integration
    |
    */

    'base_url' => env('EVOLUTION_API_URL', 'http://localhost:8080'),
    
    'api_key' => env('EVOLUTION_API_KEY'),
    
    'webhook_url' => env('APP_URL') . '/api/webhooks/evolution',
    
    'timeout' => env('EVOLUTION_API_TIMEOUT', 30),
    
    'retry_attempts' => env('EVOLUTION_API_RETRY_ATTEMPTS', 3),
    
    'retry_delay' => env('EVOLUTION_API_RETRY_DELAY', 1000), // milliseconds
    
];
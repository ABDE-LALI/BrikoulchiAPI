<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'], // API-specific paths
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins' => [env("BASE_FRONT_URL", 'http://localhost:5173')], // Your React frontend
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Required for Sanctum cookies
];
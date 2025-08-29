<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'forgot-password', 'reset-password'],

    'allowed_methods' => ['*'],

    // Allow frontend origin explicitly
    'allowed_origins' => ['http://localhost:5173','https://smart-payroll.netlify.app'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Enable if you’re using Sanctum or sending credentials like cookies
    'supports_credentials' => true,
];

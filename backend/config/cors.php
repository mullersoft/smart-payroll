<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'forgot-password', 'reset-password'],

    'allowed_methods' => ['*'],

    // Allow frontend origin explicitly
   'allowed_origins' => [
    'https://smart-payroll.netlify.app',
    'http://localhost:5173',
],
'supports_credentials' => true,


    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Enable if you’re using Sanctum or sending credentials like cookies
    'supports_credentials' => true,
];

<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*'],
    'allowed_methods' => ['*'],

    // Do NOT use ['*'] with credentials. Use explicit origin(s) or patterns.
    'allowed_origins' => [
        'https://cfb.kennethmckrola.com',
    ],
    // Or, if you want to allow all subdomains on HTTPS:
    // 'allowed_origin_patterns' => ['https://*.kennethmckrola.com'],

    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
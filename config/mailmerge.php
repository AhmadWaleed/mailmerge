<?php
return [
    'services' => [
        'mailgun' => [
            'api_key' => env('MAILGUN_API_KEY'),
            'api_domain' => env('MAILGUN_API_DOMAIN'),
            'api_endpoint' => env('MAILGUN_API_ENDPOINT', 'https://api.mailgun.net'),
            'api_base_url' => env('MAILGUN_API_BASE_URL'),
        ],
        'pepipost' => [
            'api_key' => env('PEPIPOST_API_KEY'),
            'api_endpoint' => env('PEPIPOST_API_ENDPOINT'),
        ],
        'sendgrid' => [
            'api_key' => env('SENDGRID_API_KEY'),
            'api_endpoint' => env('SENDGRID_API_ENDPOINT'),
        ],
    ],
];
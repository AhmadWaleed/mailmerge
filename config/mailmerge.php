<?php
return [
    /*
    |--------------------------------------------------------------------------
    | MailMerge Path
    |--------------------------------------------------------------------------
    |
    | This is the URI prefix where Wink will be accessible from. Feel free to
    | change this path to anything you like.
    |
    */
    'path' => env('MAILMERGE_PATH', 'wink'),

    'services' => [
        'default' => env('DEFAULT_SERVICE', 'mailgun'),
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

    /*
    |--------------------------------------------------------------------------
    | Wink Middleware Group
    |--------------------------------------------------------------------------
    |
    | This is the middleware group that wink use.
    | By default is the web group a correct one.
    | It need at least the next middlewares
    | - StartSession
    | - ShareErrorsFromSession
    |
    */
    'middleware_group' => env('MAILMERGE_MIDDLEWARE_GROUP', 'web'),
];
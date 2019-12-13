<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default mail service client
    |--------------------------------------------------------------------------
    */
    'default_service' => env('DEFAULT_SERVICE', 'mailgun'),

    /*
    |--------------------------------------------------------------------------
    | Mailmerge Middleware Group
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
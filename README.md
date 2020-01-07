## Introduction
The mail service is meant to provide a handful of APIs to send all sorts of emails including (batch mails, single mail) using different mail service providers such as mailgun, pepipost, and sendgrid. It also defines the strategy for resending the failed emails using different mail services.

## Installation

Add package in your composer.json repositories

```
"repositories": [
    {
        "type": "path",
        "url": "./path-to-your-local-package"
    }
]
```

now add package in your composer.json require packages and run `composer update`

```sh
"require": {
    "ahmedwaleed/mailmerge": "dev-master"
},
```
MailMerge will automatically register itself using [package discovery](https://laravel.com/docs/packages#package-discovery).

Once Composer is done, run the following command, it will migrate mailmerge migrations:

```sh
php artisan mailmerge:migrate
```
## Requirements
- PHP >= 7.4
- Laravel >= 6.0
- Redis

MailMerge uses redis for saving events and logs so you must have redis install on your host.

# Usage
This package registered all api endpoint you need, run `php artisan route:list` to see all available endpoints.

| Method        | URI           | Action| Middleware |
| ------------- |:-------------:| -----:| ---------: |
| GET HEAD       | api/log | MailMerge\Http\Controllers\Api\MailLogsController@index |MailMerge\Http\Middleware\ApiAuth,MailMerge\Http\Middleware\ClientSwitcher |
| POST      | api/logs/mailgun-webhook      |   MailMerge\Http\Controllers\Api\MailgunWebhookController@handle |Mailmerge\Http\Middleware\VerifyMailgunWebhook|
| POST | api/logs/pepipost-webhook      |    MailMerge\Http\Controllers\Api\PepipostWebhookController@handle | |
| POST | api/logs/sendgrid-webhook      |    MailMerge\Http\Controllers\Api\SendGridWebhookController@handle | |
| POST | api/mails/batch      |    MailMerge\Http\Controllers\Api\SendBatchController@handle | MailMerge\Http\Middleware\ApiAuth,MailMerge\Http\Middleware\ClientSwitcher |
| POST | api/mails/message      |    MailMerge\Http\Controllers\Api\SendMailMessageController@handle | MailMerge\Http\Middleware\ApiAuth,MailMerge\Http\Middleware\ClientSwitcher |
| POST | mailmerge/resend-batch      |    MailMerge\Http\Controllers\ResendBatchController@handle | web,Illuminate\Auth\Middleware\Authenticate |   


## Authentication
You must pass an authorization signature in headers when calling APIs.
```json
"headers": {
    "signature": "90gMPhN7Q3bQYJgFGZufYo7y6DLSSDDurEvFO4EFksA="
}
```

## Send Email Message

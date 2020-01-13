## Introduction
MailMerge is meant to provide a handful of APIs to send all sorts of emails including (batch mails, single mail) using different mail service providers such as mailgun, pepipost, and sendgrid. It also defines the strategy for resending the failed emails using different mail services.

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
MailMerge uses very simple authentication method all you have to do is pass an authorization signature in the request header.
```json
{
    "signature": "90gMPhN7Q3bQYJgFGZufYo7y6DLSSDDurEvFO4EFksA="
}
```

## Send Batch Message

* Api Endpoint
```
/api/mails/batch
```

* Example Request Parameters

```json
{ 
   "recipients":[ 
      { 
         "email": "john.doe@example.com",
         "attributes": {
            "first":"John",
            "last":"Doe",
            "id":"1"
         }
      },
      {
        "email": "sally.doe@example.com",
         "attributes": {
            "first":"sally",
            "last":"Doe",
            "id":"2"
         }
      }
   ],
   "from":"janedoe@example.com",
   "subject":"Hey <%attribute.first%>",
   "body":"If you wish to unsubscribe,click https://domain.com/unsubscribe/<%attribute.id%>"
}
```

* Example Response

```json 
{ 
   "status":200,
   "message":"Batch message processed successfully."
}
```

* Example Code

```php
$payload = [
    'from' => 'john.snow@thewall.north',
    'recipients' => [
            [
                'email' => 'john_doe@example.com',
                'attributes' => [
                'first' => 'john',
                'last' => 'doe'
            ],
        ]
    ],
    'subject' => 'Hi <%attribute.first%>',
    'body' => 'This this test body with last name <%attribute.last%>.'
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://domain.com/api/mails/message");
        
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "signature: 80PhN7Q3bQFSDFDSF333fYo7y6DLSSDDKKDK885dvFO4EFksA="
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch)
```


<dl>
  <dt>service</dt>
  <dd>
    You can spcify service in the api headers which you want to use for sending message
    For example:
    
```php
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "API-SERVICE: sendgrid", // suported (mailgun, pepipost, sendgrid)
]);
```
   <dt>service</dt>
   <DD>
    MailMerge also allows you to add cc and bcc to your message, you can add those in your request body.
    For example.
    
```json
{
    "cc": "cc1@example.com,cc2@example.com",
    "bcc": "bcc1@example.com,bcc2@example.com",
}
```

## Introduction
MailMerge is meant to provide a handful of APIs to send all sorts of emails including (batch mails, single mail) using different mail service providers such as mailgun, pepipost, and sendgrid. It also defines the strategy for resending the failed emails using different mail services.

## Requirements
- PHP >= 7.4
- Laravel >= 6.0
- Redis

MailMerge uses redis for saving events and logs so you must have redis install on your host.

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

## Configuring the package

You can publish the config file with:

```bash
php artisan vendor:publish --tag="mailmerge-config"
```

This is the contents of the file that will be published at `config/mailmerge.php`:

```php
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
    'path' => env('MAILMERGE_PATH', 'mailmerge'),

    /*
    |--------------------------------------------------------------------------
    | MailMerge Services Credentials
    |--------------------------------------------------------------------------
    |
    */
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
    | MailMerge Middleware Group
    |--------------------------------------------------------------------------
    |
    | This is the middleware group that mailmerge uses for package web routes.
    |
    */
    'middleware_group' => env('MAILMERGE_MIDDLEWARE_GROUP', 'web'),
];
```

Please set your required env vars for all services specified in the config files. `'default' => env('DEFAULT_SERVICE', 'mailgun')` set this options for your default service which will be used when no service is is explictilry specified when using mailmerge api.

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
            [
                'email' => 'jane_doe@example.com',
                'attributes' => [
                'first' => 'jane',
                'last' => 'doe'
            ]
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
   <dt>cc, bcc</dt>
   <dd>
    MailMerge also allows you to add cc and bcc to your message, you can add those in your request body.
    For example.
    
```json
{
    "cc": "cc1@example.com,cc2@example.com",
    "bcc": "bcc1@example.com,bcc2@example.com",
}
```
   <dt>attachment</dt>
   <dd>
    Wtih MailMerge you can send one or more attachments with batch message.
    
```php
$payload = ['https://site.com/uploads/sample1.pdf', 'https://site.com/uploads/sample2.pdf'];
```
<dl>

* Batch Mail Template

MailMerge uses its own email placeholdrer template for recipients custom attributes (variables) because of different integration of MSP so you don't have to worry about syntax different services.

- Syntax
`<% attribute.custom_attribute_name %>`
- Example

```
'recipients' => [
    [
        'email' => 'jane_doe@example.com',
        'attributes' => [
        'id' => '1',
        'first' => 'jane',
        'last' => 'doe'
    ]
]
```
```
Subject: Hey <%attribute.first%> <%attribute.last%>  <%attribute.id%>
```
## Re-Send Batch Message

* Api Endpoint
```
/resend-batch
```
Sending a batch message can be a time taking task on user end, it may take time for you to arrange all the recipents list that you want to send as batch message and what if your batch message failed so instead of doing all the havy work again and again with MailMerge its now possible to resend your batch message using different service (mailgun, pepipost, sendgrid). MailMerge saves all of your sent batch messages so you can retry that batch message in future in case of some system failure or if there are so many failed or bounced emails, when you retry or resend batch message it handle the logic of sending that batch message only for failed recipients from last batch so you can retry that batch message as many times as you want until all the emails from that batch are sent successfully.

MailMerge provide a default view for handling your batch messages.
![resend batch message default view](https://lh3.googleusercontent.com/j9voc8ErhsRqlUDXQdFIubbDmAe_Kjr75uAiQjgKAViqNRj_WtwPQgzxICg2A5h0yIkcAcQUhhbox1dpkikNSMWsLOZ-fcCeIlYiGbHjJ6En1Sli6ek1fNQm5Zcs7vwylGq60_jUeO6OsBZUucjbAjI9gphaKCZ9RQPGBT6LL38Ac5DT1oO1zBNRudqky2RMwZr7H-QextqLudS5_8Cr6lunJrJQboaqbz5q1aeL7ma4SS8fzocr4bqT_Ba0r2WZAoSVPIZyv-fAyQ8m09PJU3L4E_671mFNulnMY0E-3WqBkuLIIaOL40RcSUKJ_bzSlNTfL2mfFhlg1DX8ozKDUc1avQZENjjAS7a2ZNHW6LCMAexiGTUZruPNXjkxpoDGIhxDHMki9znuRhmWnRqW8-u_wP2Y0wkf3KagPxbnI30-vqMihN3CmTzsVhq801Sy8JlaPi635SYXCfSfnRTEbF21nkraNl4QE7NOhGWpHvIeC-YgSV9d8EyfsBzJhVxkLHVHV_J9GmJuNZiAkcdHH78c6cldftZpE1YjqIOVM9h2ya7uqLQgwfxXjVnKwPbWZMrdRuKhJckvwhKgmcPnlO7hkAi0FY5NWlLMCvQuTswBskw2VLeTEiQKDLdIWs5872VUxDDnIfioHnkhYPJu_y__uCAZctvYLJd4THBaHLauH_r1TwHpwFZlWsZbNgjxXxOyW7L9yRkAHIA1dHxt-lkLbWvb7Qh2Ll5irU8N3heJ_vg=w888-h306-no)
but you're free to modify it as per your need.
You can run below command to publish MailMerge default view of resinding batch messages.
```bash
php artisan vendor:publish --tag='mailmerge-views' --force
```
Once the view is published you may find it inside `resources/views/vendors/mailmerge` directory.

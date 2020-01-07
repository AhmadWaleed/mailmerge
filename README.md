## Introduction
The mail service is meant to provide a handful of APIs to send all sorts of emails including (batch mails, single mail) using different mail service providers such as mailgun, pepipost, and sendgrid. It also defines the strategy for resending the failed emails using different mail services.

## Installation

Add package as a repository in composer.json file.

```
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:AhmadWaleed/mailmerge.git"
    }
]
```

now require mailmerge package and run `composer update`

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

#Usage
This package registered all api endpoint you need, run `php artisan route:list` to see all available endpoints.
```

```

## Authentication
You must pass an authorization signature in headers when calling APIs.
```json
"headers": {
    "signature": "90gMPhN7Q3bQYJgFGZufYo7y6DLSSDDurEvFO4EFksA="
}
```

## Send Email Message

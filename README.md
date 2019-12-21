## Installation
First add package as a repository in composer.json file.

```
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:AhmadWaleed/mailmerge.git"
    }
]
```

Now you can install MailMerge via composer using the following command:

```sh
composer require ahmedwaleed/mailmerge
```
MailMerge will automatically register itself using [package discovery](https://laravel.com/docs/packages#package-discovery)

## Requirements
Mailmerge requires a Laravel application running version 6.0 or higher and php minimum php 7.4
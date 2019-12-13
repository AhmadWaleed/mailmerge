<?php

namespace Mailmerge\Services\Mailgun;

use Mailgun\Mailgun;
use Mailgun\HttpClient\HttpClientConfigurator;

class MailgunClientFactory
{
    public static function make()
    {
        $configurator = new HttpClientConfigurator();
        $configurator->setApiKey(config('mail.mailgun.api_key'));
        $configurator->setEndpoint(config('mail.mailgun.api_endpoint'));

        $mailgun = new Mailgun($configurator);

        return new MailgunClient(
            $mailgun,
            config('mail.mailgun.api_domain')
        );
    }
}
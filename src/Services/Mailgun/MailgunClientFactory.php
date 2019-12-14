<?php

namespace MailMerge\Services\Mailgun;

use Mailgun\Mailgun;
use Mailgun\HttpClient\HttpClientConfigurator;

class MailgunClientFactory
{
    public static function make()
    {
        $configurator = new HttpClientConfigurator();
        $configurator->setApiKey(config('mailmerge.services.mailgun.api_key'));
        $configurator->setEndpoint(config('mailmerge.services.mailgun.api_endpoint'));

        $mailgun = new Mailgun($configurator);

        return new MailgunClient(
            $mailgun,
            config('mailmerge.services.mailgun.api_domain')
        );
    }
}
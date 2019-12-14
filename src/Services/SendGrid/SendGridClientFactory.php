<?php

namespace MailMerge\Services\SendGrid;

use MailMerge\MailClient;

class SendGridClientFactory
{
    public static function make(): MailClient
    {
        return new SendGridClient(
            new \SendGrid(config('mailmerge.services.sendgrid.api_key'))
        );
    }
}
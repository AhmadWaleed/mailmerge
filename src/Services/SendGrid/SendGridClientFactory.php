<?php

namespace Mailmerge\Services\SendGrid;

use Mailmerge\Repositories\MailLogsRepository;
use Mailmerge\MailClient;

class SendGridClientFactory
{
    public static function make(): MailClient
    {
        return new SendGridClient(
            new \SendGrid(config('mail.sendgrid.api_key')),
            app(MailLogsRepository::class)
        );
    }
}
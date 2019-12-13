<?php

use Mailmerge\MailClient;
use Mailmerge\Services\Mailgun\MailgunClient;
use Mailmerge\Services\Mailgun\MailgunClientFactory;
use Mailmerge\Services\Pepipost\PepipostClient;
use Mailmerge\Services\Pepipost\PepipostClientFactory;
use Mailmerge\Services\SendGrid\SendGridClient;
use Mailmerge\Services\SendGrid\SendGridClientFactory;
use Illuminate\Support\Str;

function get_mail_client($service = null): MailClient
{
    $service = $service ?: config('mailmerge.default_service');

    switch ($service) {
        case 'mailgun':
            return MailgunClientFactory::make();
        case 'pepipost':
            return PepipostClientFactory::make();
        case 'sendgrid':
            return SendGridClientFactory::make();
        default:
            throw new \RuntimeException('No mail service configured!');
    }
}

function get_current_service(): string
{
    switch (get_class(app(MailClient::class))) {
        case MailgunClient::class:
            return 'mailgun';
        case PepipostClient::class:
            return 'pepipost';
        case SendGridClient::class:
            return 'sendgrid';
        default:
            throw new \RuntimeException('No service is currently active!');
    }
}

function get_attachment_from_url(string $url, $prefix = '', bool $usePrefixAsName = false): string
{
    $filename = $usePrefixAsName ? $prefix : $prefix . Str::random();

    $path = storage_path($filename . '.' . Str::after(basename($url), '.'));

    if (! copy($url, $path)) {
        throw new \RuntimeException("Failed to resolve attachment!");
    }

    return $path;
}

function get_filename($url): string
{
    return strtok(basename($url), '.');
}
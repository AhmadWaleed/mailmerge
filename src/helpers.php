<?php

use MailMerge\MailClient;
use Illuminate\Support\Str;
use MailMerge\Services\Mailgun\MailgunClient;
use MailMerge\Services\Mailgun\MailgunClientFactory;
use MailMerge\Services\Pepipost\PepipostClient;
use MailMerge\Services\Pepipost\PepipostClientFactory;
use MailMerge\Services\SendGrid\SendGridClient;
use MailMerge\Services\SendGrid\SendGridClientFactory;

function get_mail_client($service = null): MailClient
{
    $service = $service ?: config('mailmerge.services.default');

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

function get_client_service(MailClient $client = null): string
{
    switch (get_class($client ?: app(MailClient::class))) {
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
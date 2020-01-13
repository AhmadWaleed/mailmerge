<?php

use MailMerge\MailClient;
use Illuminate\Support\Str;
use MailMerge\NullableMailClient;
use MailMerge\Services\Mailgun\MailgunClientFactory;
use MailMerge\Services\Pepipost\PepipostClientFactory;
use MailMerge\Services\SendGrid\SendGridClientFactory;

function get_mail_client($service = null): MailClient
{
    switch ($service) {
        case 'mailgun':
            return MailgunClientFactory::make();
        case 'pepipost':
            return PepipostClientFactory::make();
        case 'sendgrid':
            return SendGridClientFactory::make();
        default:
            return new NullableMailClient();
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
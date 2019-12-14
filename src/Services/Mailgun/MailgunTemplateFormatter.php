<?php

namespace MailMerge\Services\Mailgun;

use MailMerge\TemplateFormatter;

class MailgunTemplateFormatter extends TemplateFormatter
{
    public function format(string $body): string
    {
        $replacements = ['%', 'recipient.', '%'];

        return preg_replace(self::PATTERNS, $replacements, $body);
    }
}
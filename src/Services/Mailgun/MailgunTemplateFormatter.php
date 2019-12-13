<?php

namespace Mailmerge\Services\Mailgun;

use Mailmerge\TemplateFormatter;

class MailgunTemplateFormatter extends TemplateFormatter
{
    public function format(string $body): string
    {
        $replacements = ['%', 'recipient.', '%'];

        return preg_replace(self::PATTERNS, $replacements, $body);
    }
}
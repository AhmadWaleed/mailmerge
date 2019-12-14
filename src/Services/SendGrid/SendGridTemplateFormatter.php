<?php

namespace MailMerge\Services\SendGrid;

use MailMerge\TemplateFormatter;

class SendGridTemplateFormatter extends TemplateFormatter
{
    public function format(string $body): string
    {
        $replacements = ['%', '', '%'];

        return preg_replace(self::PATTERNS, $replacements, $body);
    }
}
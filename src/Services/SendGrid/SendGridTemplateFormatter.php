<?php

namespace Mailmerge\Services\SendGrid;

use Mailmerge\TemplateFormatter;

class SendGridTemplateFormatter extends TemplateFormatter
{
    public function format(string $body): string
    {
        $replacements = ['%', '', '%'];

        return preg_replace(self::PATTERNS, $replacements, $body);
    }
}
<?php

namespace MailMerge\Services\Pepipost;

use MailMerge\TemplateFormatter;

class PepipostTemplateFormatter extends TemplateFormatter
{
    public function format(string $body): string
    {
        $replacements = ['[%', '', '%]'];

        return preg_replace(self::PATTERNS, $replacements, $body);
    }
}
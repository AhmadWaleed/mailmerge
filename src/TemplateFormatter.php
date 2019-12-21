<?php

namespace MailMerge;

use MailMerge\Services\Mailgun\MailgunTemplateFormatter;
use MailMerge\Services\Pepipost\PepipostTemplateFormatter;
use MailMerge\Services\SendGrid\SendGridTemplateFormatter;

abstract class TemplateFormatter
{
    public const MAILGUN = MailgunTemplateFormatter::class;
    public const PEPIPOST = PepipostTemplateFormatter::class;
    public const SENDGRID = SendGridTemplateFormatter::class;

    protected const PATTERNS = ['/<\s*%\s*/', '/attribute./', '/\s*%\s*>/'];

    abstract public function format(string $body): string;
}
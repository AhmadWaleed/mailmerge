<?php

namespace Mailmerge;

use Mailmerge\Services\Mailgun\MailgunTemplateFormatter;
use Mailmerge\Services\Pepipost\PepipostTemplateFormatter;
use Mailmerge\Services\SendGrid\SendGridTemplateFormatter;

abstract class TemplateFormatter
{
    public const MAILGUN = MailgunTemplateFormatter::class;
    public const PEPIPOST = PepipostTemplateFormatter::class;
    public const SENDGRID = SendGridTemplateFormatter::class;

    protected const PATTERNS = ['/<\s*%/', '/attribute./', '/%\s*>/'];

    abstract public function format(string $body): string;
}
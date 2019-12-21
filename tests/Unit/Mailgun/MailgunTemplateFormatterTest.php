<?php

namespace Mailmerge\Tests\Unit\Mailgun;

use MailMerge\Services\Mailgun\MailgunTemplateFormatter;
use MailMerge\TemplateFormatter;
use PHPUnit\Framework\TestCase;

class MailgunTemplateFormatterTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideAllPossibleInputs
     */
    public function it_formats_mailgun_email_template($input, $output)
    {
        $formatter = new MailgunTemplateFormatter();

        $this->assertSame($formatter->format($input), $output);
    }

    public function provideAllPossibleInputs()
    {
        return [
            'template without any whitespace' => ['<%attribute.username%>', '%recipient.username%'],
            'template without whitespaces in between' => ['<% attribute.user_name %>', '%recipient.user_name%'],
            'template with more then one whitespaces' => ['<%  attribute.user-name   %>', '%recipient.user-name%'],
            'template with left inner whitespace and right without spaces' => ['<% attribute.user.name%>', '%recipient.user.name%'],
        ];
    }
}
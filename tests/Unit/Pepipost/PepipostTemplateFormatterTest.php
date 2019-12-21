<?php

namespace Mailmerge\Tests\Unit\Pepipost;

use PHPUnit\Framework\TestCase;
use MailMerge\Services\Pepipost\PepipostTemplateFormatter;

class PepipostTemplateFormatterTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideAllPossibleInputs
     */
    public function it_formats_mailgun_email_template($input, $output)
    {
        $formatter = new PepipostTemplateFormatter();

        $this->assertSame($formatter->format($input), $output);
    }

    public function provideAllPossibleInputs()
    {
        return [
            'template without any whitespace' => ['<%attribute.username%>', '[%username%]'],
            'template without whitespaces in between' => ['<% attribute.user_name %>', '[%user_name%]'],
            'template with more then one whitespaces' => ['<%  attribute.user-name   %>', '[%user-name%]'],
            'template with left inner whitespace and right without spaces' => ['<% attribute.user.name%>', '[%user.name%]'],
        ];
    }
}
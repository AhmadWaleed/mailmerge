<?php

namespace MailMerge\Tests;

use MailMerge\BatchMessage;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function parameters(array $extras = [])
    {
        return [
            "from" => "jhon.snow@thewall.north",
            "subject" => "Hey John",
            "body" => "Test email body.",
            "to" => "john.doe@example.com",
            ...$extras
        ];
    }

    public function fakeBatchMessage()
    {
        return (new BatchMessage())
            ->setFromAddress('john@example.com')
            ->setSubject('Test Subject')
            ->setTextBody('Test Body.')
            ->setBatchIdentifier(rand(0, 5))
            ->addAttachments(['http://site.attachment1.txt', 'http://site.attachment1.pdf'])
            ->setToRecipients([
                [
                    "email" => "john.doe@example.com",
                    'attributes' => ["first" => "John", "last" => "Doe", "id" => "1"]
                ],
                [
                    "email" => "jane.doe@example.com",
                    'attributes' => ["first" => "Jane", "last" => "Doe", "id" => "2"]
                ]
            ]);
    }
}
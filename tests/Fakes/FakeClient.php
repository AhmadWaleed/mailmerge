<?php

namespace MailMerge\Tests\Fakes;

use MailMerge\BatchMessage;
use MailMerge\MailClient;
use PHPUnit\Framework\Assert;

class FakeClient implements MailClient
{
    /**
     * @inheritDoc
     */
    public function sendMessage(array $parameters): void
    {
        Assert::assertTrue(true);
    }

    /**
     * @inheritDoc
     */
    public function sendBatch(BatchMessage $message): void
    {
        Assert::assertTrue(true);
    }

    /**
     * @inheritDoc
     */
    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void
    {
        Assert::assertTrue(true);
    }
}
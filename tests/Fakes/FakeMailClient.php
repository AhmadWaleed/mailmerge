<?php

namespace Tests;

use MailMerge\BatchMessage;
use MailMerge\MailClient;

class FakeMailClient implements MailClient
{
    public function sendMessage(array $parameters): void
    {
    }

    public function sendBatch(BatchMessage $message, bool $resend = false): void
    {
    }

    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void
    {
    }
}
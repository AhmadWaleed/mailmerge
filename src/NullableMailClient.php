<?php

namespace MailMerge;

class NullableMailClient implements MailClient
{
    /**
     * @inheritDoc
     */
    public function sendMessage(array $parameters): void
    {
    }

    /**
     * @inheritDoc
     */
    public function sendBatch(BatchMessage $message): void
    {
    }

    /**
     * @inheritDoc
     */
    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void
    {
     }
}
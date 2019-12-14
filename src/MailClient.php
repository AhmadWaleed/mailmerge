<?php

declare(strict_types=1);

namespace MailMerge;

interface MailClient
{
    /**
     * send single email message
     *
     * @param array $parameters email message parameters
     *
     * @return void
     */
    public function sendMessage(array $parameters): void;

    /**
     * Send batch message
     *
     * @param BatchMessage $message batch message object
     *
     * @return void
     */
    public function sendBatch(BatchMessage $message): void;

    /**
     * Resend batch message for failed messages
     *
     * @param BatchMessage $message batch message object that will be resend
     * @param MailClient $client mail client for resending batch message
     * @param array $options array of options for resend batch message
     *
     * @return void
     */
    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void;
}
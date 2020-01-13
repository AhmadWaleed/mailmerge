<?php

namespace MailMerge\Jobs;

use MailMerge\MailClient;
use Illuminate\Support\Facades\Log;

class ProcessMailMessage extends Job
{
    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    public array $message;

    public string $client;

    public function __construct(string $client, array $message)
    {
        $this->client = $client;
        $this->message = $message;
    }

    /**
     * Execute single mail message.
     */
    public function handle(): void
    {
        $mailClient = get_mail_client($this->client);

        try {
            $mailClient->sendMessage($this->message);
        } catch (\Exception $exception) {
            Log::error("Failed to send email message:", [
                'client' => get_class($mailClient),
                'message' => $this->message,
                'exception' => $exception->getMessage()
            ]);

            $this->fail($exception);
        }

        Log::debug('Email message sent successfully.', [
            'client' => get_class($mailClient),
            'message' => $this->message,
        ]);
    }
}

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

    public MailClient $mailClient;

    public function __construct(MailClient $mailClient, array $message)
    {
        $this->message = $message;
        $this->mailClient = $mailClient;
    }

    /**
     * Execute single message.
     */
    public function handle(): void
    {
        try {
            $this->mailClient->sendMessage($this->message);
        } catch (\Exception $e) {
            Log::error("Failed to send email message:", [
                'client' => get_class($this->mailClient),
                'message' => $this->message,
                'exception' => $e->getMessage()
            ]);
        }

        Log::debug('Email message sent successfully.', [
            'client' => get_class($this->mailClient),
            'message' => $this->message,
        ]);
    }
}

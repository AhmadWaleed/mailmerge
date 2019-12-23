<?php

namespace MailMerge\Jobs;

use MailMerge\Batch;
use MailMerge\MailClient;
use MailMerge\BatchMessage;
use Illuminate\Support\Facades\Log;

class ProcessBatchMessage extends Job
{
    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    public BatchMessage $message;

    public MailClient $mailClient;

    private bool $resending = false;

    public function __construct(MailClient $mailClient, BatchMessage $message, bool $resending = false)
    {
        $this->message = $message;
        $this->mailClient = $mailClient;
        $this->resending = $resending;
    }

    /**
     * Execute single batch message.
     */
    public function handle(): void
    {
        $this->mailClient->sendBatch($this->message);

        if (! $this->resending) {
            Batch::record($this->message, get_client_service($this->mailClient));
        }

        Log::debug('Batch mail proceed successfully.', [
            'client' => get_class($this->mailClient)
        ]);
    }
}

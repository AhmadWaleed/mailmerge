<?php

namespace MailMerge\Jobs;

use MailMerge\Batch;
use MailMerge\BatchMessage;
use Illuminate\Support\Facades\Log;

class ProcessBatchMessage extends Job
{
    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    public BatchMessage $message;

    public string $client;

    private bool $resending = false;

    public function __construct(string $client, BatchMessage $message, bool $resending = false)
    {
        $this->client = $client;
        $this->message = $message;
        $this->resending = $resending;
    }

    /**
     * Execute single batch message.
     */
    public function handle(): void
    {
        $mailClient = get_mail_client($this->client);

        try {
            $mailClient->sendBatch($this->message);
        } catch (\Exception $exception) {
            Log::critical('Batch message failed!', [
                'client' => get_class($mailClient),
                'message' => $this->message->toArray(),
                'exception' => $exception->getMessage()
            ]);

            $this->fail($exception);
        }

        if (! $this->resending) {
            Batch::record($this->message, $mailClient->toString());
        }

        Log::debug('Batch mail proceed successfully.', [
            'client' => get_class($mailClient),
            'message' => $this->message->toArray(),
        ]);
    }
}

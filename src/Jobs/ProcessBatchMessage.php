<?php

namespace Mailmerge\Jobs;

use Mailmerge\Services\MailClient;
use Mailmerge\Services\BatchMessage;
use Illuminate\Support\Facades\Log;

class ProcessBatchMessage extends Job
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /** @var BatchMessage */
    public $message;

    /** @var MailClient */
    public $mailClient;

    public function __construct(MailClient $mailClient, BatchMessage $message)
    {
        $this->message = $message;
        $this->mailClient = $mailClient;
    }

    /**
     * Execute single batch message.
     */
    public function handle(): void
    {
        $this->mailClient->sendBatch($this->message);

        Log::debug('Batch mail proceed successfully.', [
            'client' => get_class($this->mailClient)
        ]);
    }
}

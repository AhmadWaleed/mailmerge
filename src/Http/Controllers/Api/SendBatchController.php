<?php

declare(strict_types=1);

namespace MailMerge\Http\Controllers\Api;

use MailMerge\MailClient;
use MailMerge\Jobs\ProcessBatchMessage;
use MailMerge\Http\Requests\BatchRequest;

class SendBatchController
{
    public function handle(BatchRequest $request, MailClient $mailClient)
    {
        foreach ($request->batchMessages() as $batchMessage) {
            dispatch(new ProcessBatchMessage($mailClient, $batchMessage));
        }

        return response()->json(['message' => 'Batch message processed successfully.']);
    }
}

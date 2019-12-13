<?php

declare(strict_types=1);

namespace Mailmerge\Http\Controllers\V1;

use Mailmerge\Jobs\ProcessBatchMessage;
use Mailmerge\Http\Requests\BatchRequest;
use Mailmerge\MailClient;

class SendBatchController
{
    public function handle(BatchRequest $request, MailClient $mailClient)
    {
        foreach ($request->batchMessages() as $batchMessage) {
            dispatch(new ProcessBatchMessage($mailClient, $batchMessage));
        }

        return response()->json(['message' => 'success']);
    }
}

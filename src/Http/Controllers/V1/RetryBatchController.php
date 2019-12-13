<?php

namespace Mailmerge\Http\Controllers\V1;

use Mailmerge\Batch;
use Mailmerge\Services\MailClient;
use Illuminate\Http\Request;

class RetryBatchController
{
    public function handle(Request $request, MailClient $mailClient)
    {
        $batch = Batch::findOrFail($request->get('batch_id'));

        $batchMessage = $mailClient->getFailedRecipients($batch->batch_message);

        dispatch(new ProcessBatchMessage($batchMessage));
    }
}

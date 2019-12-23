<?php

namespace MailMerge\Http\Controllers;

use MailMerge\Batch;
use Illuminate\Http\Request;

class ResendBatchController
{
    public function handle(Request $request)
    {
        /** @var Batch $batch */
        $batch = Batch::findOrFail($request->get('batch_id'));

        $mailClient = $batch->mailClient();

        $mailClient->resendBatch($batch->batch_message, $request->mailClient());

        $batch->markAsResend();

        return redirect('/settings');
    }
}

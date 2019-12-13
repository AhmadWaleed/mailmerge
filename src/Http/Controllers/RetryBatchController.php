<?php

namespace Mailmerge\Http\Controllers;

use Mailmerge\Batch;
use Illuminate\Http\Request;

class RetryBatchController
{
    public function handle(Request $request)
    {
        $batch = Batch::findOrFail($request->get('batch_id'));

        $mailClient = get_mail_client($batch->service);

        $mailClient->resendBatch($batch->batch_message, get_mail_client($request->service), [
            'start_date' => $batch->created_at->format('Y-m-d')
        ]);

        $batch->markAsRetried();

        return redirect('/settings');
    }
}
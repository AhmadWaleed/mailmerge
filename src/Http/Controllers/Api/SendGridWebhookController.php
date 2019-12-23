<?php

namespace MailMerge\Http\Controllers\Api;

use Illuminate\Http\Request;
use MailMerge\Services\SendGrid\SendGridLog;
use MailMerge\Repositories\MailLogsRepository;

class SendGridWebhookController
{
    public function handle(Request $request, MailLogsRepository $logsRepository)
    {
        foreach ($request->all() as $event)
        {
            $sendGridLog = SendGridLog::fromEvent($event);

            if (in_array($event['event'], ['bounce', 'dropped'])) {
                $logsRepository->saveLogs($sendGridLog->toJson(), 'sendgrid_failed:logs');
            }

            $logsRepository->saveLogs($sendGridLog->toJson(), 'sendgrid:logs');
        }

        return response()->json();
    }
}

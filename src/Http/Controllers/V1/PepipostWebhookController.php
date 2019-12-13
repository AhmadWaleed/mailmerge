<?php

namespace Mailmerge\Http\Controllers\V1;

use Mailmerge\Services\Pepipost\PepipostLog;
use Illuminate\Http\Request;
use Mailmerge\Repositories\MailLogsRepository;

class PepipostWebhookController
{
    public function handle(Request $request, MailLogsRepository $logsRepository)
    {
        foreach ($request->all() as $payload) {
            /** @var PepipostLog $pepipostLog */
            $pepipostLog = PepipostLog::fromEvent($payload);

            $logsRepository->saveLogs($pepipostLog->toJson(), 'pepipost:logs');
        }

        return response()->json();
    }
}
<?php

namespace MailMerge\Http\Controllers\Api;

use MailMerge\Services\Pepipost\PepipostLog;
use Illuminate\Http\Request;
use MailMerge\Repositories\MailLogsRepository;

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

<?php

declare(strict_types=1);

namespace MailMerge\Http\Controllers\Api;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MailMerge\Repositories\MailLogsRepository;

class MailLogsController
{
    use ValidatesRequests;

    public function index(Request $request, MailLogsRepository $logsRepository)
    {
        $this->validate($request, [
            'service' => 'required|in:mailgun,pepipost,sendgrid',
            'items' => 'sometimes|integer',
        ]);

        $key = "{$request->service}:logs";

        $logs = $logsRepository->getLogs(0, (int) $request->get('items', 10), $key);

        return response()->json($logs);
    }
}

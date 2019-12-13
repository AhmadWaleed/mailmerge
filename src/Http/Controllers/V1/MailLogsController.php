<?php

declare(strict_types=1);

namespace Mailmerge\Http\Controllers\V1;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Mailmerge\Repositories\MailLogsRepository;

class MailLogsController
{
    use ValidatesRequests;

    public function index(Request $request, MailLogsRepository $logsRepository)
    {
        $this->validate($request, [
            'service' => 'required|in:mailgun,pepipost',
            'items' => 'sometimes|integer',
        ]);

        $key = "{$request->service}:logs";

        $logs = $logsRepository->getLogs(0,(int) $request->get('items', 10), $key);

        return response()->json($logs);
    }
}
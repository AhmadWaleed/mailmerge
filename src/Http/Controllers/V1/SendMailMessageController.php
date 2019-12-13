<?php

namespace Mailmerge\Http\Controllers\V1;

use Mailmerge\MailClient;
use Illuminate\Http\Request;
use Mailmerge\Jobs\ProcessMailMessage;
use Mailmerge\Services\Pepipost\PepipostClient;
use Illuminate\Foundation\Validation\ValidatesRequests;

class SendMailMessageController
{
    use ValidatesRequests;

    public function handle(Request $request, MailClient $mailClient)
    {
        $this->validate($request, [
            'from' => 'required|email',
            'to' => 'required|email',
            'subject' => 'required',
            'body' => 'required',
            'attachment' => 'sometimes|required|url',
        ]);

        if ($mailClient instanceof PepipostClient) {
            $mailClient->sendMessage($request->all());
        } else {
            dispatch(new ProcessMailMessage($mailClient, $request->all()));
        }

        return response()->json(['message' => 'success']);
    }
}
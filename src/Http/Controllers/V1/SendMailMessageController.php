<?php

namespace Mailmerge\Http\Controllers\V1;

use Mailmerge\Services\MailClient;
use Mailmerge\Services\Pepipost\PepipostClient;
use Illuminate\Http\Request;
use Mailmerge\Jobs\ProcessMailMessage;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class SendMailMessageController
{
    use ProvidesConvenienceMethods;

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
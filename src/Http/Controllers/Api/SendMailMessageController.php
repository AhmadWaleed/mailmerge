<?php

namespace MailMerge\Http\Controllers\Api;

use MailMerge\MailClient;
use Illuminate\Http\Request;
use MailMerge\Jobs\ProcessMailMessage;
use Illuminate\Validation\ValidationException;
use MailMerge\Services\Pepipost\PepipostClient;
use Illuminate\Foundation\Validation\ValidatesRequests;

class SendMailMessageController
{
    use ValidatesRequests;

    public function handle(Request $request, MailClient $mailClient)
    {
        try {
            $this->validate($request, [
                'from' => 'required|email',
                'to' => 'required|email',
                'subject' => 'required',
                'body' => 'required',
                'attachment' => 'sometimes|required|url',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), $e->status);
        }

        dispatch(new ProcessMailMessage($mailClient->toString(), $request->all()));

        return response()->json(['message' => 'Email has been sent successfully.']);
    }
}

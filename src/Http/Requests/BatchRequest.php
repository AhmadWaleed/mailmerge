<?php

namespace Mailmerge\Http\Requests;

use Mailmerge\Services\BatchMessage;
use Illuminate\Http\Request;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class BatchRequest
{
    use ProvidesConvenienceMethods;

    /** @var Request */
    public $request;

    public function __construct(Request $request)
    {
        Log::debug('payload', $request->all());
        $this->request = $request;

        $this->validate($this->request, $this->rules());
    }

    public function rules()
    {
        return [
            'from' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
            'recipients' => 'required',
            'attachments' => 'sometimes|string',
        ];
    }

    public function batchMessages()
    {
        return LazyCollection::make(function () {
            yield from $this->recipients();
        })
        ->chunk(1000)
        ->map(function($recipients) {
            $message = new BatchMessage();
            $message->setFromAddress($this->request->from)
                ->setSubject($this->request->subject)
                ->setToRecipients($recipients->toArray())
                ->setTextBody($this->request->body)
                ->addAttachments($this->attachments());

            return $message;
        });
    }

    public function attachments(): array
    {
        if ($this->request->has('attachments') && $this->request->filled('attachments')) {
            return json_decode($this->request->attachments, true);
        }

        return [];
    }

    public function recipients(): array
    {
        return $this->request->recipients;
    }
}

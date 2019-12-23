<?php

namespace MailMerge\Http\Requests;

use MailMerge\BatchMessage;
use Illuminate\Support\LazyCollection;
use Illuminate\Foundation\Http\FormRequest;

class BatchRequest extends FormRequest
{
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
            $message->setFromAddress($this->from)
                ->setSubject($this->subject)
                ->setToRecipients($recipients->toArray())
                ->setTextBody($this->body)
                ->addAttachments($this->attachments());

            return $message;
        });
    }

    public function attachments(): array
    {
        if ($this->has('attachments') && $this->filled('attachments')) {
            return json_decode($this->attachments, true);
        }

        return [];
    }

    public function recipients(): array
    {
        return $this->recipients;
    }
}

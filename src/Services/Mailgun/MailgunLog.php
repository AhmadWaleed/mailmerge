<?php

namespace MailMerge\Services\Mailgun;

use MailMerge\BaseMailLog;

class MailgunLog extends BaseMailLog
{
    public static function fromEvent($event): self
    {
        $self = new static();
        $self->originalResponse = $event;

        $self->normalizedResponse['status'] = $event['event'] ?? '';
        $self->normalizedResponse['to_email'] = $event['envelope']['targets'] ??
            $event['message']['headers']['to'] ?? $event['recipient'] ?? '';
        $self->normalizedResponse['from_email'] = $event['envelope']['sender']['from'] ?? '';
        $self->normalizedResponse['unique_identifier'] = $event['message']['headers']['message-id'] ?? '';

        return $self;
    }
}
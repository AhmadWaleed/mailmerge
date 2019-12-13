<?php

namespace Mailmerge\Services\SendGrid;

use Mailmerge\BaseMailLog;

class SendGridLog extends BaseMailLog
{
    public static function fromEvent($event): self
    {
        $self = new static();

        $self->originalResponse = $event;

        $self->normalizedResponse['status'] = $event['event'] ?? '';
        $self->normalizedResponse['to_email'] = $event['email'] ?? '';
        $self->normalizedResponse['from_email'] = '';
        $self->normalizedResponse['unique_identifier'] = '';

        return $self;
    }
}
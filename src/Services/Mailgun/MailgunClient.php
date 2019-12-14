<?php

declare(strict_types=1);

namespace MailMerge\Services\Mailgun;

use MailMerge\MailClient;
use MailMerge\TemplateFormatter;
use Mailgun\Mailgun;
use MailMerge\BatchMessage;
use Mailgun\Message\Exceptions\TooManyRecipients;
use Mailgun\Model\Event\Event;

class MailgunClient implements MailClient
{
    protected Mailgun $mailgun;

    protected string $domain;

    public function __construct(Mailgun $mailgun, string $domain)
    {
        $this->domain = $domain;
        $this->mailgun = $mailgun;
    }

    public function sendBatch(BatchMessage $message): void
    {
        /** @var \Mailgun\Message\BatchMessage $batchMessage */
        $batchMessage = $this->mailgun->messages()
            ->getBatchMessage($this->domain)
            ->setFromAddress($message->from())
            ->setSubject($message->subject(TemplateFormatter::MAILGUN))
            ->setTextBody($message->body(TemplateFormatter::MAILGUN));

        foreach ($message->attachments() as $attachment) {
            $batchMessage->addAttachment($attachment);
        }

        foreach ($message->recipients() as $recipient) {
            try {
                $batchMessage->addToRecipient($recipient['email'], $recipient['attributes']);
            } catch (TooManyRecipients $exception) {
                continue;
            }
        }

        $batchMessage->finalize();

        [$messageId] = $batchMessage->getMessageIds();

        $message->setBatchIdentifier(trim($messageId, '<>'));
    }

    public function sendMessage(array $parameters): void
    {
        $attachment = null;

        if (isset($parameters['attachment'])) {
            $attachment = get_attachment_from_url($parameters['attachment']);
        }

        $message = collect($parameters)->mapWithKeys(function ($value, $key) use ($attachment) {
            if ($key === 'body') {
                return ['text' => $value];
            }

            if ($key === 'attachment' && ! is_null($attachment)) {
                return [$key => [[
                    'filePath' => $attachment,
                    'filename' => get_filename($value)
                ]]];
            }

            return [$key => $value];
        });

        try {
            $this->mailgun->messages()->send($this->domain, $message->toArray());
        } finally {
            if (isset($attachment) && file_exists($attachment)) {
                unlink($attachment);
            }
        }
    }

    public function getEvents(array $query = [])
    {
        return $this->mailgun->events()->get($this->domain, $query);
    }

    public function resendBatch(BatchMessage $batchMessage, MailClient $client, array $options = []): void
    {
        $response =  $this->getEvents([
            'event' => 'rejected OR failed',
            'limit' =>  count($batchMessage->recipients()),
            'message-id' => $batchMessage->batchIdentifier(),
        ]);

        $failedRecipients = collect($response->getItems())->map(function (Event $event) {
            return $event->getEnvelope()['targets']
                ?? $event->getMessage()['headers']['to']
                ?? $event->getRecipient() ?? '';
        });

        if ($failedRecipients->isEmpty()) {
            throw new \RuntimeException('No failed recipients found against given batch message');
        }

        $recipients = collect($batchMessage->recipients())->reject(function ($recipient) use ($failedRecipients) {
            return ! in_array($recipient['email'], $failedRecipients->toArray());
        });

        $batchMessage->setToRecipients($recipients->toArray());

        $client->sendBatch($batchMessage);
    }
}

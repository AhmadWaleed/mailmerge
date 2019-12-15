<?php

declare(strict_types=1);

namespace MailMerge\Services\Mailgun;

use MailMerge\Attachment;
use MailMerge\MailClient;
use MailMerge\TemplateFormatter;
use Mailgun\Mailgun;
use MailMerge\BatchMessage;
use Mailgun\Message\Exceptions\TooManyRecipients;
use Mailgun\Model\Event\Event;
use Spatie\TemporaryDirectory\TemporaryDirectory;

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
        $filePath = null;
        $attachment = null;

        if (isset($parameters['attachment'])) {
            $attachment = new Attachment();
            $filePath = $attachment->fromUrl($parameters['attachment'])->save();
        }

        $message = collect($parameters)->mapWithKeys(function ($value, $key) use ($filePath) {
            if ($key === 'body') {
                return ['text' => $value];
            }

            if ($key === 'attachment' && ! is_null($filePath)) {
                return [$key => [[
                    'filePath' => $filePath,
                    'filename' => get_filename($value)
                ]]];
            }

            return [$key => $value];
        });

        try {
            $this->mailgun->messages()->send($this->domain, $message->toArray());
        } finally {
            if (! is_null($attachment)) {
                $attachment->getDirectory()->delete();
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

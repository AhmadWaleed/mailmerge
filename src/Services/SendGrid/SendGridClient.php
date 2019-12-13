<?php

namespace Mailmerge\Services\SendGrid;

use Mailmerge\Batch;
use SendGrid\Mail\Mail;
use Illuminate\Support\Arr;
use Mailmerge\MailClient;
use SendGrid\Mail\Attachment;
use Mailmerge\BatchMessage;
use Mailmerge\TemplateFormatter;
use Mailmerge\Repositories\MailLogsRepository;
use Mailmerge\Repositories\RedisMailLogsRepository as RedisMailLogs;

class SendGridClient implements MailClient
{
    /** @var \SendGrid */
    private $sendGridClient;

    /** @var MailLogsRepository */
    private $logsRepository;

    public function __construct(\SendGrid $sendGridClient, MailLogsRepository $logsRepository)
    {
        $this->sendGridClient = $sendGridClient;
        $this->logsRepository = $logsRepository;
    }

    public function sendMessage(array $parameters): void
    {
        $attachment = get_attachment_from_url($parameters['attachment']);

        $email = new Mail();

        $email->setFrom($parameters['from']);
        $email->addTo($parameters['to']);
        $email->setSubject($parameters['subject']);
        $email->addContent('text/plain', $parameters['body']);

        if (isset($parameters['attachment'])) {
            $email->addAttachment(new Attachment(
                file_get_contents($attachment),
                mime_content_type($attachment),
                get_filename($parameters['attachment'])
            ));
        }

        try {
            $this->sendGridClient->send($email);
        } finally {
            if (file_exists($attachment)) {
                unlink($attachment);
            }
        }
    }

    public function sendBatch(BatchMessage $message, bool $resend = false): void
    {
        $batch = new Mail();

        $message->setBatchIdentifier(md5(microtime().rand()));

        $batch->setBatchId($message->batchIdentifier());

        $batch->setFrom($message->from());
        $batch->setSubject($message->subject(TemplateFormatter::SENDGRID));
        $batch->addContent('text/plain', $message->body(TemplateFormatter::SENDGRID));

        foreach ($message->recipients() as $recipient) {
            $email = $recipient['email'];
            $name = strtok($email, '@');
            $batch->addTo($email, $name, $this->mapKeys(Arr::except($recipient, 'email')));
        }

        foreach ($message->attachments() as $attachment) {
            $batch->addAttachment(base64_encode(file_get_contents($attachment)));
        }

        $this->sendGridClient->send($batch);

        if (! $resend) {
            Batch::record($message, 'sendgrid');
        }
    }

    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void
    {
        $logs = $this->logsRepository->getLogs(RedisMailLogs::FIRST, RedisMailLogs::LAST, 'sendgrid_failed:logs');

        $failedRecipients = collect($logs)->map(function ($log) {
            return data_get(json_decode($log, true), 'normalized_response.to_email');
        });

        if ($failedRecipients->isEmpty()) {
            throw new \RuntimeException('No failed recipients found against given batch message');
        }

        $recipients = collect($message->recipients())->reject(function ($recipient) use ($failedRecipients) {
            return ! in_array($recipient['email'], $failedRecipients->toArray());
        });

        $message->setToRecipients($recipients->toArray());

        // delete failed logs next time failed logs list does not
        // contains failed logs from previous sent batch
        $this->logsRepository->deleteKey('sendgrid_failed:logs');

        $client->sendBatch($message, true);
    }

    public function mapKeys(array $attributes): array
    {
        return collect($attributes)->mapWithKeys(function ($value, $key) {
            return ["%{$key}%" => $value];
        })->toArray();
    }
}

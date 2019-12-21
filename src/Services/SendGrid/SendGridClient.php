<?php

namespace MailMerge\Services\SendGrid;

use GuzzleHttp\Client;
use SendGrid\Mail\Mail;
use MailMerge\MailClient;
use SendGrid\Mail\Attachment;
use MailMerge\BatchMessage;
use MailMerge\TemplateFormatter;

class SendGridClient implements MailClient
{
    public const BATCH_IDENTIFIER_ARG = 'batch_identifier_arg';

    private \SendGrid $sendGridClient;

    private Client $client;

    private string $apiKey;

    public function __construct(\SendGrid $sendGridClient)
    {
        $this->sendGridClient = $sendGridClient;

        $this->apiKey = config('services.sendgrid.api_endpoint', 'https://api.sendgrid.com/v3');

        $this->client = new Client(['base_uri' => $this->apiKey]);
    }

    public function sendMessage(array $parameters): void
    {
        $attachment = get_attachment_from_url($parameters['attachment']);

        $email = new Mail();

        $email->setFrom($parameters['from']);
        $email->addTo($parameters['to']);
        $email->setSubject($parameters['subject']);
        $email->addContent('text/plain', $parameters['body']);

        if (isset($parameters['cc'])) {
            foreach (explode(',', $parameters['cc']) as $ccEmail) {
                $email->addCc(trim($ccEmail));
            }
        }

        if (isset($parameters['bcc'])) {
            foreach (explode(',', $parameters['bcc']) as $bccEmail) {
                $email->addBcc(trim($bccEmail));
            }
        }

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

    public function sendBatch(BatchMessage $message): void
    {
        $batch = new Mail();

        $message->setBatchIdentifier(md5(microtime().rand()));

        $batch->addCustomArg(self::BATCH_IDENTIFIER_ARG, $message->batchIdentifier());

        $batch->setFrom($message->from());
        $batch->setSubject($message->subject(TemplateFormatter::SENDGRID));
        $batch->addContent('text/plain', $message->body(TemplateFormatter::SENDGRID));

        foreach ($message->recipients() as $recipient) {
            $email = $recipient['email'];
            $name = strtok($email, '@');
            $batch->addTo($email, $name, $this->mapKeys($recipient['attributes']));
        }

        foreach ($message->attachments() as $attachment) {
            $batch->addAttachment(base64_encode(file_get_contents($attachment)));
        }

        $batch->addCustomArg('batch_identifier', 'custom_batch_arg');

        $this->sendGridClient->send($batch);
    }

    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void
    {
        $queryString = "(status='not_delivered' OR status='bounced') AND (unique_args['".self::BATCH_IDENTIFIER_ARG."']='{$message->batchIdentifier()}')";
        $query = urlencode($queryString);

        $response = $this->client->request('GET', "/messages?{$query}&limit=" . count($message->recipients()), [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}"
            ]
        ]);

        $decodedResponse = json_decode($response->getBody(), true);

        $failedRecipients = collect($decodedResponse['messages'])->map(function ($message) {
            return $message['to_email'];
        });

        if ($failedRecipients->isEmpty()) {
            throw new \RuntimeException('No failed recipients found against given batch message');
        }

        $recipients = collect($message->recipients())->reject(function ($recipient) use ($failedRecipients) {
            return ! in_array($recipient['email'], $failedRecipients->toArray());
        });

        $message->setToRecipients($recipients->toArray());

        $client->sendBatch($message);
    }

    public function mapKeys(array $attributes): array
    {
        return collect($attributes)->mapWithKeys(function ($value, $key) {
            return ["%{$key}%" => $value];
        })->toArray();
    }
}

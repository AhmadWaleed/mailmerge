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

    public function __construct(\SendGrid $sendGridClient, ?Client $client = null)
    {
        $this->sendGridClient = $sendGridClient;

        $this->apiKey = config('mailmerge.services.sendgrid.api_key');

        $this->client = $client ?: new Client([
            'base_uri' => config('mailmerge.services.sendgrid.api_endpoint', 'https://api.sendgrid.com/v3')
        ]);
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

        $this->sendGridClient->send($batch);
    }

    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void
    {
        $queryString = "(status='not_delivered' OR status='bounced') AND (unique_args['".self::BATCH_IDENTIFIER_ARG."']='{$message->batchIdentifier()}')";
        $query = urlencode($queryString);

        try {
            $response = $this->client->request('GET', "/v3/messages?query={$query}&limit=" . count($message->recipients()), [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}"
                ]
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Cannot resend batch message failed to get failed recipients: {$e->getMessage()}");
        }

        $decodedResponse = json_decode($response->getBody(), true);

        $failedRecipients = collect($decodedResponse['messages'])->map(fn ($message) => $message['to_email']);

        $messageRecipients = collect($message->recipients())->map(fn ($recipient) => $recipient['email']);

        $failedRecipients = $failedRecipients->reject(
            fn ($recipient) => ! in_array($recipient, $messageRecipients->toArray())
        );

        if ($failedRecipients->isEmpty()) {
            throw new \RuntimeException('No failed recipients found against given batch message');
        }

        $recipients = collect($message->recipients())->reject(function ($recipient) use ($failedRecipients) {
            return ! in_array($recipient['email'], $failedRecipients->toArray());
        });

        $message->setToRecipients($recipients->toArray());

        $client->sendBatch($message);
    }

    protected function mapKeys(array $attributes): array
    {
        return collect($attributes)->mapWithKeys(function ($value, $key) {
            return ["%{$key}%" => $value];
        })->toArray();
    }

    public function toString(): string
    {
        return 'sendgrid';
    }
}

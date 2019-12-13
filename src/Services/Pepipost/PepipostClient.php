<?php

namespace Mailmerge\Services\Pepipost;

use Mailmerge\Batch;
use Mailmerge\BatchMessage;
use Mailmerge\MailClient;
use Mailmerge\TemplateFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PepipostClient implements MailClient
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $apiKey;

    public function __construct(Client $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function sendMessage(array $parameters): void
    {
        $this->request('sendEmail', [
            'personalizations' => [
                ['recipient' => $parameters['to']]
            ],
            'from' => $this->from($parameters['from']),
            'subject' => $parameters['subject'],
            'content' => $parameters['body'],
            'attachments' => isset($parameters['attachment']) ? $this->attachments($parameters['attachment']) : [],
            'settings' => $this->settings(),
        ]);
    }

    public function sendBatch(BatchMessage $message, bool $resend = false): void
    {
        $message->setBatchIdentifier(md5(microtime().rand()));

        $this->request('sendEmail', [
            'personalizations' => $this->personalizations($message),
            'from' => $this->from($message->from()),
            'subject' => $message->subject(TemplateFormatter::PEPIPOST),
            'content' => $message->body(TemplateFormatter::PEPIPOST),
            'attachments' => $this->attachments($message->attachments()),
            'settings' => $this->settings(),
        ]);

        if (! $resend) {
            Batch::record($message, 'pepipost');
        }
    }

    private function personalizations(BatchMessage $message): array
    {
        $personalizations = [];

        foreach ($message->recipients() as $recipient) {
            $personalizations[] = [
                'recipient' => $recipient['email'],
                'attributes' => Arr::except($recipient, 'email'),
                'x-apiheader' => $message->batchIdentifier(),
            ];
        }

        return $personalizations;
    }

    private function from($email, $name = null): array
    {
        return [
            'fromEmail' => $email,
            'fromName' => $name ?: strtok($email, '@'),
        ];
    }

    private function attachments($urls): array
    {
        $urls = is_array($urls) ? $urls : Arr::wrap($urls);

        $attachments = [];

        foreach ($urls as $url) {
            $attachment = get_attachment_from_url($url);
            $attachments[] = [
                'fileContent' => base64_encode(file_get_contents($attachment)),
                'fileName' => get_filename($url),
            ];

            if (file_exists($attachment)) {
                unlink($attachment);
            }
        }

        return $attachments;
    }

    private function request($uri, $payload, string $method = 'POST'): bool
    {
        try {
            $this->client->request($method, $uri, array_merge(['json' => $payload,], $this->authHeader()));

            return true;
        } catch (GuzzleException $e) {
            Log::critical("Pepipost batch mail failed", [$e->getMessage()]);
            return false;
        }
    }

    protected function authHeader(): array
    {
        return ['headers' => ['api_key' => $this->apiKey]];
    }

    private function settings(array $overrides = []): array
    {
        return array_merge([
            'footer' => 0,
            'clicktrack' => 0,
            'opentrack' => 1,
            'unsubscribe' => 0,
        ], $overrides);
    }

    public function resendBatch(BatchMessage $message, MailClient $client, array $options = []): void
    {
        $query = array_merge(['query' => [
            'events' => 'bounce,dropped',
            'startdate' => $options['start_date'] ?? '',
            'xapiheader' => $message->batchIdentifier(),
        ]], $this->authHeader());

        $response = $this->client->get('logs', $query);

        $decodedResponse = json_decode($response->getBody(), true);

        if ($decodedResponse['status'] !== 'success') {
            throw new \RuntimeException("Something went wrong while retrying batch from pepipost");
        }

        $failedRecipients = collect($decodedResponse['data'])->map(function ($event) {
            return $event['rcptEmail'];
        });

        if ($failedRecipients->isEmpty()) {
            throw new \RuntimeException('No failed recipients found against given batch message');
        }

        $recipients = collect($message->recipients())->reject(function ($recipient) use ($failedRecipients) {
            return ! in_array($recipient['email'], $failedRecipients->toArray());
        });

        $message->setToRecipients($recipients->toArray());

        $client->sendBatch($message, true);
    }
}

<?php

namespace Mailmerge\Tests\Unit;

use Mailgun\Api\Message;
use Mailgun\Mailgun;
use Mailgun\Message\MessageBuilder;
use MailMerge\BatchMessage;
use MailMerge\Services\Mailgun\MailgunClient;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class MailgunClientTest extends TestCase
{
    /**
     * @var Mailgun|m\LegacyMockInterface|m\MockInterface
     */
    private $mailgun;

    private string $domain;

    private MailgunClient $mailgunClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mailgun = m::mock(Mailgun::class);
        $this->domain = 'http://test.mailgun.com';

        $this->mailgunClient = new MailgunClient($this->mailgun, $this->domain);
    }

    private function parameters(array $extras = [])
    {
        return [
            "from" => "jhon.snow@thewall.north",
            "subject" => "Hey John",
            "body" => "Test email body.",
            "to" => "john.doe@example.com",
            ...$extras
        ];
    }

    /** @test */
    public function it_sends_batch_message()
    {
        $batchMessage = m::mock(\Mailgun\Message\BatchMessage::class);
        $messageBuilder = m::mock(MessageBuilder::class);

        $messages = m::mock(Message::class);
        $messages->shouldReceive('getBatchMessage')->andReturn($batchMessage);

        $batchMessage->shouldReceive('setFromAddress')->andReturnSelf();
        $batchMessage->shouldReceive('setSubject')->andReturnSelf();
        $batchMessage->shouldReceive('setTextBody')->andReturnSelf();
        $batchMessage->shouldReceive('addAttachment')->andReturn($messageBuilder);
        $batchMessage->shouldReceive('addToRecipient')->andReturn($messageBuilder);
        $batchMessage->shouldReceive('finalize')->andReturn(true);
        $batchMessage->shouldReceive('getMessageIds')->andReturn(['batch-id']);

        $this->mailgun->shouldReceive('messages')->andReturn($messages);

        $message = new BatchMessage();
        $message->setFromAddress('john@example.com');
        $message->setSubject('Test Subject');
        $message->setTextBody('Test Body.');
        $message->setToRecipients([
                [
                    "email" => "john.doe@example.com",
                    'attributes' => ["first" => "John", "last" => "Doe", "id" => "1"]
                ],
                [
                    "email" => "jane.doe@example.com",
                    'attributes' => ["first" => "Jane", "last" => "Doe", "id" => "2"]
                ]
        ]);
        $message->addAttachments(['http://site.attachment1.txt', 'http://site.attachment1.pdf']);

        $this->mailgunClient->sendBatch($message);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_sends_mail_message()
    {
        $messages = m::mock(Message::class);
        $messages->shouldReceive('send')->andReturn(true);

        $this->mailgun->shouldReceive('messages')->andReturn($messages);

        $this->mailgunClient->sendMessage($this->parameters());

        $this->assertTrue(true);
    }
}

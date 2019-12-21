<?php

namespace Mailmerge\Tests\Unit\Mailgun;

use Mailgun\Api\Event;
use Mailgun\Api\Message;
use Mailgun\Mailgun;
use Mailgun\Message\MessageBuilder;
use Mailgun\Model\Event\Event as MailgunEvent;
use MailMerge\Services\Mailgun\MailgunClient;
use MailMerge\Tests\BaseTestCase;
use MailMerge\Tests\Fakes\FakeClient;
use Mockery as m;

class MailgunClientTest extends BaseTestCase
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

        $message = $this->fakeBatchMessage();

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

    /** @test */
    public function it_resend_batch_message()
    {
        $message = $this->fakeBatchMessage();

        $eventsResponse = [
            MailgunEvent::create([
                'event' => 'failed',
                'id' => rand(1, 5),
                'timestamp' => time(),
                'envelope' => ['targets' => 'john@example.com']
            ]),
            MailgunEvent::create([
                'event' => 'rejected',
                'id' => rand(1, 5),
                'timestamp' => time(),
                'envelope' => ['targets' => 'jane@example.com']
            ])
        ];

        $events = m::mock(Event::class, ['get' => m::mock(['getItems' => $eventsResponse])]);

        $this->mailgun->shouldReceive('events')->andReturn($events);

        $mailClient = new FakeClient();

        $this->mailgunClient->resendBatch($message, $mailClient);
    }

    /** @test */
    public function it_except_when_there_is_no_failed_recipient_while_resending()
    {
        $message = $this->fakeBatchMessage();

        $events = m::mock(Event::class, ['get' => m::mock(['getItems' => []])]);

        $this->mailgun->shouldReceive('events')->andReturn($events);

        $mailClient = new FakeClient();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No failed recipients found against given batch message');

        $this->mailgunClient->resendBatch($message, $mailClient);
    }
}

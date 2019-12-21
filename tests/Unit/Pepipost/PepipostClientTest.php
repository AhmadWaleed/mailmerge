<?php

namespace Mailmerge\Tests\Unit\Pepipost;

use GuzzleHttp\Client;
use MailMerge\Services\Pepipost\PepipostClient;
use MailMerge\Tests\BaseTestCase;
use Mockery as m;

class PepipostClientTest extends BaseTestCase
{
    /**
     * @var Client|m\LegacyMockInterface|m\MockInterface
     */
    private $client;

    private PepipostClient $pepipostClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = m::mock(Client::class);

        $this->pepipostClient = new PepipostClient($this->client, 'test_api_key');
    }

    /** @test */
    public function it_sends_mail_message()
    {
        $this->client->shouldReceive('request')->andReturnTrue();

        $this->pepipostClient->sendMessage($this->parameters());

        $this->assertTrue(true);
    }
}
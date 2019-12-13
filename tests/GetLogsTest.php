<?php

namespace Tests;

use Illuminate\Support\Facades\Redis;

class GetLogsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        Redis::lpush(
            'mailgun:logs', 'log1', 'log2', 'log3', 'log4', 'log5', 'log6', 'log7', 'log8'
        );
    }

    protected function tearDown(): void
    {
        Redis::flushall();
    }

    /** @test */
    public function it_gets_most_ten_recent_logs()
    {
        $response = $this->get('/v1/logs?service=mailgun', $this->authHeaders());

        $response->assertJson([
            "log8", "log7", "log6", "log5", "log4", "log3", "log2", "log1",
        ]);
    }

    /** @test */
    public function it_gets_recent_logs_with_requested_amount_of_items()
    {
        $response = $this->get('/v1/logs?items=3&service=mailgun', $this->authHeaders());

        $response->assertJson([
            "log8", "log7", "log6", "log5",
        ]);
    }

    /** @test */
    public function it_returns_empty_array_when_there_is_no_saved_logs()
    {
        $this->tearDown();

        $response = $this->get('/v1/logs?items=3&service=mailgun', $this->authHeaders());

        $this->assertSame($response->decodeResponseJson(), []);
    }
}
<?php

namespace Tests;

use Mailmerge\MailmergeServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [MailmergeServiceProvider::class];
    }

    public function authHeaders()
    {
        return ['API-KEY' => 'test-key'];
    }
}

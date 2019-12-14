<?php

namespace Tests;

use MailMerge\MailMergeServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [MailMergeServiceProvider::class];
    }

    public function authHeaders()
    {
        return ['API-KEY' => 'test-key'];
    }
}

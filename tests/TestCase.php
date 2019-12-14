<?php

namespace MailMerge\Tests;

use MailMerge\MailMergeServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [MailMergeServiceProvider::class];
    }
}

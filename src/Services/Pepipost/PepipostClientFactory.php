<?php

namespace Mailmerge\Services\Pepipost;

use GuzzleHttp\Client;

class PepipostClientFactory
{
    public static function make()
    {
        $client = new Client([
            'base_uri' => config('mail.pepipost.api_endpoint')
        ]);

        return new PepipostClient($client, config('mail.pepipost.api_key'));
    }
}
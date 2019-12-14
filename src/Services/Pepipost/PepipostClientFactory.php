<?php

namespace MailMerge\Services\Pepipost;

use GuzzleHttp\Client;

class PepipostClientFactory
{
    public static function make()
    {
        $client = new Client([
            'base_uri' => config('mailmerge.services.pepipost.api_endpoint')
        ]);

        return new PepipostClient($client, config('mailmerge.services.pepipost.api_key'));
    }
}
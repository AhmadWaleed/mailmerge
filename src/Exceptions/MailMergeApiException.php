<?php

namespace Mailmerge\Exceptions;

class MailMergeApiException extends \Exception
{
    public static function noApiKey()
    {
        return new static('No api key provided!');
    }

    public static function invalidApiKey()
    {
        return new static('Api key is not valid!');
    }
}
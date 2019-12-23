<?php

namespace MailMerge\Exceptions;

class MailMergeApiException extends \Exception
{
    public static function noApiKey()
    {
        return new static('Invalid request no api key provided!');
    }

    public static function invalidApiKey()
    {
        return new static('Unauthorized! invalid api key provided!');
    }
}

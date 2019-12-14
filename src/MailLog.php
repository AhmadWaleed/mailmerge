<?php

namespace MailMerge;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

interface MailLog extends Jsonable, Arrayable
{
    public static function fromEvent($event);
}
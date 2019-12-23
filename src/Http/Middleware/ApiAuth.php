<?php

namespace MailMerge\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MailMerge\Exceptions\MailMergeApiException;

class ApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasHeader('API-KEY')) {
            throw MailMergeApiException::noApiKey();
        }

        if ($request->header('API-KEY') === config('mailmerge.signature')) {
            throw MailMergeApiException::invalidApiKey();
        }

        return $next($request);
    }
}

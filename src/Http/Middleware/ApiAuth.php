<?php

namespace Mailmerge\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mailmerge\Exceptions\MailMergeApiException;
use Mailmerge\User;

class ApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->header('API-KEY')) {
            throw MailMergeApiException::noApiKey();
        }

        if (User::validApiKey($request->header('API-KEY'))) {
            throw MailMergeApiException::invalidApiKey();
        }

        return $next($request);
    }
}

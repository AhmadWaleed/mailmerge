<?php

namespace Mailmerge\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VerifyMailgunWebhook
{
    /**
     * Verify an incoming webhook request from mail service provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->has('signature')) {
            throw new UnauthorizedHttpException("Unauthorized!");
        }

        if (! $this->valid($request->signature)) {
            throw new UnauthorizedHttpException("Unauthorized!");
        }

        return $next($request);
    }

    private function valid(array $signature): bool
    {
        // check if the timestamp is fresh
        if (abs(time() - $signature['timestamp']) > 15) {
            return false;
        }

        $payload = $signature['timestamp'] . $signature['token'];

        // returns true if signature is valid
        return hash_hmac('sha256', $payload, config('mail.mailgun.api_key')) === $signature['signature'];
    }
}

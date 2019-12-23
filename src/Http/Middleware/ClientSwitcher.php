<?php

namespace MailMerge\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use MailMerge\MailClient;

class ClientSwitcher
{
    public Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next)
    {
        switch ($request->header('API-SERVICE')) {
            case 'mailgun':
                $this->app->bind(MailClient::class, function () {
                    return get_mail_client('mailgun');
                });
                break;

            case 'pepipost':
                $this->app->bind(MailClient::class, function () {
                    return get_mail_client('pepipost');
                });
                break;

            case 'sendgrid':
                $this->app->bind(MailClient::class, function () {
                    return get_mail_client('sendgrid');
                });
                break;

            default:
                $this->app->bind(MailClient::class, function () {
                    return get_mail_client();
                });
                break;
        }

        return $next($request);
    }
}

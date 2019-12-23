<?php

namespace MailMerge;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use MailMerge\Http\Middleware\ApiAuth;
use MailMerge\Http\Middleware\ClientSwitcher;
use Mailmerge\Http\Middleware\VerifyMailgunWebhook;
use MailMerge\Repositories\MailLogsRepository;
use MailMerge\Repositories\RedisMailLogsRepository;

class MailMergeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerPublishing();

        $this->loadViewsFrom(
            __DIR__.'/../resources/views', 'mailmerge'
        );
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        $path = config('mailmerge.path');
        $middlewareGroup = config('mailmerge.middleware_group');

        Route::namespace('MailMerge\Http\Controllers\Api')
            ->prefix('api')
            ->group(function () {
                Route::group(['middleware' => [ApiAuth::class, ClientSwitcher::class]], function () {
                    Route::post('mails/batch', 'SendBatchController@handle');
                    Route::post('mails/message', 'SendMailMessageController@handle');
                    Route::post('mails/resend-batch', 'ResendBatchController@handle');
                    Route::get('logs', 'MailLogsController@index');
                });
                Route::group(['middleware' => VerifyMailgunWebhook::class], function () {
                    Route::post('logs/mailgun-webhook', 'MailgunWebhookController@handle');
                });
                Route::post('logs/pepipost-webhook', 'PepipostWebhookController@handle');
                Route::post('logs/sendgrid-webhook', 'SendGridWebhookController@handle');
            });

        Route::namespace('MailMerge\Http\Controllers')
            ->middleware([$middlewareGroup, Authenticate::class])
            ->as('mailmerge.')
            ->prefix($path)
            ->group(function () {
                Route::get('/resend-batch', [DashboardController::class, 'index'])->name('dashboard.index');
            });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mailmerge.php' => config_path('mailmerge.php'),
            ], 'mailmerge-config');
        }
    }



    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MailClient::class, function () {
            return get_mail_client(config('mailmerge.services.default'));
        });

        $this->app->bind(MailLogsRepository::class, RedisMailLogsRepository::class);

        $this->mergeConfigFrom(
            __DIR__.'/../config/mailmerge.php', 'mailmerge'
        );

        $this->commands([
            Console\ClearLogs::class,
        ]);
    }
}
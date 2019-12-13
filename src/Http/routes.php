<?php

use Illuminate\Support\Facades\Route;
use Mailmerge\Http\Middleware\ApiAuth;
use Mailmerge\Http\Middleware\ClientSwitcher;
use Mailmerge\Http\Middleware\VerifyMailgunWebhook;

Route::namespace('Mailmerge\Http\Controllers\V1')->prefix('v1')->group(function () {
    Route::group(['middleware' => [ApiAuth::class, ClientSwitcher::class]], function () {
        Route::post('mails/batch', 'SendBatchController@handle');
        Route::post('mails/message', 'SendMailMessageController@handle');

        Route::get('logs', 'MailLogsController@index');
    });

    Route::group(['middleware' => VerifyMailgunWebhook::class], function () {
        Route::post('logs/mailgun-webhook', 'MailgunWebhookController@handle');
    });

    Route::post('logs/pepipost-webhook', 'PepipostWebhookController@handle');

    Route::post('logs/sendgrid-webhook', 'SendGridWebhookController@handle');
});



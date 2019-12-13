<?php

namespace Mailmerge;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mailmerge\Repositories\MailLogsRepository;
use Mailmerge\Repositories\RedisMailLogsRepository;

class MailmergeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');

        $this->registerPublishing();

        $this->loadViewsFrom(
            __DIR__.'/../resources/views', 'wink'
        );
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
        $this->mergeConfigFrom(
            __DIR__.'/../config/mailmerge.php', 'mailmerge'
        );

        $this->registerBindings();

//        $this->commands([
//            Console\InstallCommand::class,
//            Console\MigrateCommand::class,
//        ]);
    }

    private function registerBindings()
    {
        $this->app->bind(MailClient::class,  fn() => get_mail_client());

        $this->app->bind(MailLogsRepository::class, RedisMailLogsRepository::class);
    }
}
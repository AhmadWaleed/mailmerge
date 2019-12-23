<?php

namespace MailMerge\Console;

use Illuminate\Console\Command;
use MailMerge\Repositories\MailLogsRepository;
use Illuminate\Support\Facades\Log;

class ClearLogsCommand extends Command
{
    protected $signature = 'clear:logs';

    protected $description = 'Clear old logs';

    /**
     * Execute the clear logs console command
     */
    public function handle(MailLogsRepository $logsRepository): void
    {
        $logsRepository->deleteLogs(1000, 'mailgun:logs');
        $logsRepository->deleteLogs(1000, 'pepipost:logs');
        $logsRepository->deleteLogs(1000, 'sendgrid:logs');

        Log::debug("Clear Log command ran successfully.");
    }
}

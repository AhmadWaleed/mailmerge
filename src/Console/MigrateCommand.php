<?php

namespace MailMerge\Console;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailmerge:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migrations for MailMerge';

    /**
     * Execute the clear logs console command
     */
    public function handle(): void
    {
        $this->call('migrate', [
            '--path' => 'vendor/ahmedwaleed/mailmerge/src/database/migrations',
        ]);

        $this->line('');
        $this->line('');
        $this->info('MailMerge migrations ran successfully.');
    }
}
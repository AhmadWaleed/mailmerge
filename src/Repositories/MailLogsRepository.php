<?php

namespace MailMerge\Repositories;

interface MailLogsRepository
{
    public function saveLogs(string $payload, string $key = null);

    public function getLogs(int $start, int $end, string $key = null);

    public function deleteLogs(int $keep, string $key = null);
}

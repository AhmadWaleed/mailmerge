<?php

namespace MailMerge\Repositories;

use Illuminate\Support\Facades\Redis;

class RedisMailLogsRepository implements MailLogsRepository
{
    /** @var string */
    public const KEY = 'mailgun:logs';

    /** @var int  */
    public const FIRST = 0;

    /** @var int  */
    public const LAST = -1;

    /**
     * Save logs to list
     *
     * @param string $payload
     * @param string|null $key
     *
     * @return int|false
     */
    public function saveLogs(string $payload, string $key = null)
    {
        return Redis::lpush($key ?: self::KEY, $payload);
    }

    /**
     * Get logs from speficied range
     *
     * @param int $start
     * @param int $end
     * @param string|null $key
     *
     * @return array
     */
    public function getLogs(int $start, int $end, string $key = null)
    {
        return Redis::lrange($key ?: self::KEY, $start, $end);
    }

    /**
     * Delete logs
     *
     * @param int $keep
     * @param string|null $key
     *
     * @return array
     */
    public function deleteLogs($keep = 2000, string $key = null)
    {
        return Redis::ltrim($key ?: self::KEY, self::FIRST, --$keep);
    }

    /**
     * delete given key from redis database
     *
     * @param string $key
     *
     * @return int
     */
    public function deleteKey(string $key): int
    {
        return Redis::del($key);
    }
}

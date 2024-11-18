<?php

namespace App\Services;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheService
{
    private readonly RedisAdapter $cache;
    public function __construct()
    {
        $redisConnection = RedisAdapter::createConnection('redis://redis');
        $this->cache = new RedisAdapter($redisConnection);
    }

    public function get(string $key, callable $callback): mixed
    {
        return $this->cache->get($key, $callback);
    }
}
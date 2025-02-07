<?php

namespace App\Cache;

use Illuminate\Support\Facades\Redis;

class RedisCache implements CacheInterface
{
    public function __construct(
        protected string $connectionName = 'default'
    ) {}

    private function getClient()
    {
        return Redis::connection($this->connectionName)->client();
    }

    public function save($key, $value, ?int $ttl = null): mixed
    {
        $serializedValue = json_encode($value);
        if ($serializedValue === false) {
            throw new \RuntimeException('Failed to serialize data for cache');
        }

        if (!empty($ttl)) {
            return $this->getClient()->setex($key, $ttl, $serializedValue);
        }

        return $this->getClient()->set($key, $serializedValue);
    }

    public function get($key): mixed
    {
        $value = $this->getClient()->get($key);

        if ($value === null || $value === false) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $value;
        }

        return $decoded;
    }

    public function ttl($key): int
    {
        $ttl = $this->getClient()->ttl($key);

        if ($ttl === false || $ttl < 0) {
            return 0;
        }

        return $ttl;
    }

    public function delete($key): mixed
    {
        return $this->getClient()->del($key);
    }

    public function exists($key): bool
    {
        return (bool)$this->getClient()->exists($key);
    }
}

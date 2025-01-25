<?php
namespace App\Adapters;

use Illuminate\Support\Facades\Redis;
class RedisCacheAdapter implements CacheInterface
{
    public function __construct(
        protected string $connectionName = 'default'
    ) {
    }
    private function getClient()
    {
        return Redis::connection($this->connectionName)->client();
    }
    public function save($key, $value, ?int $ttl = null): mixed
    {
        if (!empty($ttl)) {
            return $this->getClient()->setex($key, $ttl, $value);
        }
        return $this->getClient()->set($key, $value);
    }
    public function get($key): mixed
    {
        return $this->getClient()->get($key);
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

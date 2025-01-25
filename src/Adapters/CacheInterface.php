<?php
namespace App\Adapters;
interface CacheInterface
{
    public function save($key, $value, ?int $ttl = null): mixed;
    public function exists($key): bool;
    public function get($key): mixed;
    public function delete($key): mixed;
    public function ttl($key): int;
}

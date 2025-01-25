<?php

namespace App\Cache;

use App\Adapters\CacheInterface;

class ExampleCache
{
    // objectType:objectId:field
    // user:1001:name
    private const KEY = '_example:%s:%s';


    public function __construct(
        private CacheInterface $cache
    ) {
    }

    // __example_testCache
    public function save(
        string $id = '1',
        string $field = 'data',
        array $data = ['test' => 'testCache'],
        int $ttl = 86400
    )
    {
        $serializedData = json_encode($data);

        return $this->cache->save(
        // _example:1:data
            key: sprintf(self::KEY, $id , $field),
            value: $serializedData,
            ttl: $ttl
        );
    }

    public function get(string $id, string $field): ?array
    {
        $cachedData = $this->cache->get(sprintf(self::KEY, $id , $field));

        if ($cachedData !== null) {
            return json_decode($cachedData, true);
        }

        return null;
    }
    public function delete(string $id, string $field)
    {
        return $this->cache->delete(sprintf(self::KEY, $id , $field));
    }
}

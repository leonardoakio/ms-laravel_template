<?php

namespace App\Http\Controllers;

use App\Cache\ExampleCache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class HealthHandler extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'message' => 'Alive and kicking!',
            'time' => now()->toDateTimeString()
        ]);
    }

    public function liveness(): JsonResponse
    {
        $cacheInstance = app(ExampleCache::class);

        $redisStatus = $this->testRedisConnection($cacheInstance);
        $mysqlStatus = $this->testMysqlConnection();
        $mongoDbStatus = $this->testMongoDBConnection();

        return response()->json([
            'redis' => $redisStatus,
            'mysql' => $mysqlStatus,
            'mongodb' => $mongoDbStatus,
        ]);
    }

    public function testRedisConnection(ExampleCache $cache)
    {
        $driver =  config('database.redis');

        $results = [];
        $start = microtime(true);


        try {
            $cache->save();
            $cache->delete('1', 'data');

            $results[] = [

                'alive' => true,
                'host' => $driver['default']['host'],
                'duration' => $this->calculateTime($start) . ' milliseconds',
            ];
        } catch (\Throwable $e) {
            $results[] = [
                'alive' => false,
                'host' => $driver['default']['host'],
                'error' => $e->getMessage(),
                'duration' => $this->calculateTime($start) . ' milliseconds',
                ];
        }

        return $results;
    }

    protected function testMysqlConnection()
    {
        $results = [];

        $driver =  config('database.connections.mysql');
        $start = microtime(true);

        try {
            DB::connection('mysql')->select("SELECT 'Health check'");
            $results[] = [
                'alive' => true,
                'host' => $driver['host'],
                'duration' => $this->calculateTime($start) . ' milliseconds',
            ];
        } catch (\Throwable $e) {
            $results[] = [
                'alive' => false,
                'host' => $driver['host'],
                'error' => $e->getMessage(),
                'duration' => $this->calculateTime($start) . ' milliseconds',
                ];
        }

        return $results;
    }

    public function testMongoDBConnection(): array
    {
        $dsn =  config('database.connections.mongodb.dsn');

        $results = [];
        $start = microtime(true);

        try {
            $connection = DB::connection('mongodb');
            $client = $connection->getMongoClient();
            
            $db = $client->cliente_total;
            $collection = $db->testConnection;

            $document = [
                'title'   => 'Hello, world!',
                'content' => 'This is a sample document for testConnection collection.',
            ];

            // Insert $document on 'testConnection' Collection
            $collection->insertOne($document);
            // drop 'testConnection' Collection
            $collection->drop();

            $results[] = [
                'alive' => true,
                'host' => $dsn,
                'duration' => $this->calculateTime($start) . ' milliseconds',
            ];
        } catch (\Throwable $e) {
            $results[] = [
                'alive' => false,
                'host' => $dsn,
                'error' => $e->getMessage(),
                'duration' => $this->calculateTime($start) . ' milliseconds',
                ];
        }

        return $results;
    }

    protected function calculateTime(float $start): float
    {
        return round((microtime(true) - $start) * 1000);
    }
}
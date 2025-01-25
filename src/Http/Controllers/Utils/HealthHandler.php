<?php

namespace App\Http\Controllers\Utils;


use App\Cache\ExampleCache;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

        return response()->json([
            'redis' => $redisStatus,
            'mysql' => $mysqlStatus
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


    protected function calculateTime(float $start): float
    {
        return round((microtime(true) - $start) * 1000);
    }
}

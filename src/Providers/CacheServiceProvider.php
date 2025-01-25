<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Adapters\CacheInterface;
use App\Adapters\RedisCacheAdapter;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CacheInterface::class, RedisCacheAdapter::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

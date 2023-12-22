<?php

namespace App\Providers;

use App\Adapters\CacheInterface;
use App\Adapters\RedisCacheAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CacheInterface::class, RedisCacheAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

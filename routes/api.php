<?php

use App\Http\Controllers\Utils\HealthHandler;
use Illuminate\Support\Facades\Route;

// Health Check
Route::get("/health", [HealthHandler::class, "health"]);
Route::get("/liveness", [HealthHandler::class, "liveness"]);

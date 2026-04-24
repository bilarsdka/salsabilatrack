<?php

use Illuminate\Support\Str;

return [

    'default' => env('CACHE_DRIVER', 'file'),

    'stores' => [

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'),
        ],

    ],

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'),

];

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Auto-run migrations if orders table doesn't exist
        try {
            if (!Schema::hasTable('orders')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Exception $e) {
            // Database not connected yet
        }
    }
}

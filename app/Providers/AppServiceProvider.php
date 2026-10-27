<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        if (app()->environment('production') || app()->environment('local')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $directories = [
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/cache/data'),
            storage_path('logs'),
        ];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                @mkdir($directory, 0775, true);
            }
            // Always ensure the directory is writable
            @chmod($directory, 0775);
            
            // Specifically fix the log file if it exists
            $logFile = $directory . '/laravel.log';
            if ($directory === storage_path('logs') && file_exists($logFile)) {
                @chmod($logFile, 0664);
            }
        }

        // Auto-create sessions table if using database driver and table missing
        try {
            if (config('session.driver') === 'database' && !Schema::hasTable('sessions')) {
                Schema::create('sessions', function ($table) {
                    $table->string('id')->primary();
                    $table->foreignId('user_id')->nullable()->index();
                    $table->string('ip_address', 45)->nullable();
                    $table->text('user_agent')->nullable();
                    $table->longText('payload');
                    $table->integer('last_activity')->index();
                });
            }
        } catch (\Exception $e) {
            // Silently fail if DB not ready yet
        }
    }
}

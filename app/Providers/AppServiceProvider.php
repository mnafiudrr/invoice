<?php

namespace App\Providers;

use App\Contracts\PdfGenerator;
use App\Services\DompdfPdfGenerator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PdfGenerator::class, DompdfPdfGenerator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function ($job) {
            return Limit::perMinute(5)->by($job->ip());
        });

        RateLimiter::for('project-password', function ($job) {
            return Limit::perMinute(10)->by($job->ip());
        });
    }
}

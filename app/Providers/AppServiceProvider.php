<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isLocalHost = in_array(request()->getHost(), ['127.0.0.1', 'localhost', '::1', '']);

        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        } elseif (!$isLocalHost && (app()->environment('production') || str_starts_with((string) config('app.url'), 'https://'))) {
            URL::forceScheme('https');
        }
    }
}

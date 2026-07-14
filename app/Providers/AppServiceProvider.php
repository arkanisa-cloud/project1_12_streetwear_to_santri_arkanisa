<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Hanya paksa HTTPS jika aplikasi berjalan di environment 'production' (Railway)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        if (!app()->runningInConsole()) {
            if (Schema::hasTable('site_settings')) {
                View::share('siteLogo', SiteSetting::get('site_logo'));
                View::share('heroImage', SiteSetting::get('hero_image'));
            }
        }
    }
}
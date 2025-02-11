<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::registerRenderHook('head.start', function () {
            return '<link rel="icon" type="image/png" href="' . asset('favicon-16x16.png') . '">';
        });
    }
}

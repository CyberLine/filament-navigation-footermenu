<?php

namespace Cyberline\FilamentNavigationFootermenu;

use Illuminate\Support\ServiceProvider;

class FooterMenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-navigation-footermenu.php',
            'filament-navigation-footermenu',
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-navigation-footermenu');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-navigation-footermenu');

        $this->publishes([
            __DIR__ . '/../config/filament-navigation-footermenu.php' => config_path('filament-navigation-footermenu.php'),
        ], 'filament-navigation-footermenu-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-navigation-footermenu'),
        ], 'filament-navigation-footermenu-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => lang_path('vendor/filament-navigation-footermenu'),
        ], 'filament-navigation-footermenu-lang');
    }
}

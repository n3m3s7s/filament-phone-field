<?php

namespace N3m3s7s\FilamentPhoneField;

use Illuminate\Support\ServiceProvider;

class FilamentPhoneFieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-phone-field');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-phone-field');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/lang' => lang_path('vendor/filament-phone-field'),
            ], 'filament-phone-field-translations');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-phone-field'),
            ], 'filament-phone-field-views');

            $this->publishes([
                __DIR__ . '/../config/filament-phone-field.php' => config_path('filament-phone-field.php'),
            ], 'filament-phone-field-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-phone-field.php', 'filament-phone-field');
    }
}
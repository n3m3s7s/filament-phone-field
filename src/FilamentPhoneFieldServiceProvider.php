<?php

declare(strict_types=1);

namespace N3m3s7sFilamentPhoneField;

use IlluminateSupportServiceProvider;

final class FilamentPhoneFieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(
            path: __DIR__ . '/../resources/views',
            namespace: 'filament-phone-field',
        );

        $this->publishes([
            __DIR__ . '/../config/filament-phone-field.php' => config_path('filament-phone-field.php'),
        ], 'filament-phone-field-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-phone-field'),
        ], 'filament-phone-field-views');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__ . '/../config/filament-phone-field.php',
            key: 'filament-phone-field',
        );
    }
}

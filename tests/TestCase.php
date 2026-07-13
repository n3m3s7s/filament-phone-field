<?php

namespace N3m3s7s\FilamentPhoneField\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use N3m3s7s\FilamentPhoneField\FilamentPhoneFieldServiceProvider;
use Filament\FilamentServiceProvider;
use Livewire\LivewireServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FilamentPhoneFieldServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.locale', 'en');
    }
}
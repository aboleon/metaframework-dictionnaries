<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MetaFramework\Dictionnaries\DictionnariesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tests\Concerns\ResetsMultilangCache;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;
    use ResetsMultilangCache;

    protected function getPackageProviders($app): array
    {
        return [
            DictionnariesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
        $app['config']->set('app.locale', 'en');
        $app['config']->set('app.fallback_locale', 'en');

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('cache.default', 'array');

        $app['config']->set('mfw.urls.backend', 'mfw');
        $app['config']->set('mfw.translatable.multilang', true);
        $app['config']->set('mfw.translatable.locales', ['en', 'fr', 'bg']);
        $app['config']->set('mfw.translatable.active_locales', ['en', 'fr', 'bg']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetMultilangCache();
    }
}

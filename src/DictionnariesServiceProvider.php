<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries;

use Illuminate\Support\ServiceProvider;

class DictionnariesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mfw-dictionnaries.php', 'mfw-dictionnaries');
        $this->app->register(DictionnariesRouteServiceProvider::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->registerViews();
        $this->registerTranslations();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mfw-dictionnaries.php' => config_path('mfw-dictionnaries.php'),
            ], 'mfw-dictionnaries-config');

            $this->publishes([
                __DIR__ . '/Resources/views' => resource_path('views/vendor/mfw-dictionnaries'),
            ], 'mfw-dictionnaries-views');

            $this->publishes([
                __DIR__ . '/Resources/lang' => lang_path('vendor/mfw-dictionnaries'),
            ], 'mfw-dictionnaries-lang');

            $this->publishes([
                __DIR__ . '/Database/migrations' => database_path('migrations'),
            ], 'mfw-dictionnaries-migrations');

            $assetsPath = __DIR__ . '/../public/vendor/mfw-dictionnaries';
            if (is_dir($assetsPath)) {
                $this->publishes([
                    $assetsPath => public_path('vendor/mfw-dictionnaries'),
                ], 'mfw-dictionnaries-assets');
            }
        }
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom([
            resource_path('views/vendor/mfw-dictionnaries'),
            __DIR__ . '/Resources/views',
        ], 'mfw-dictionnaries');
    }

    private function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'mfw-dictionnaries');

        $appLangPath = lang_path('vendor/mfw-dictionnaries');
        if (is_dir($appLangPath)) {
            $this->loadTranslationsFrom($appLangPath, 'mfw-dictionnaries');
        }
    }
}

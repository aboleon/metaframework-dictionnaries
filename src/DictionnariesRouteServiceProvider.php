<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use MetaFramework\Accessors\Routing;
use MetaFramework\Dictionnaries\Support\RouteNaming;

class DictionnariesRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $middleware = (array) config('mfw-dictionnaries.routes.middleware', ['web']);
        $prefix = (string) (config('mfw-dictionnaries.routes.prefix')
            ?: (class_exists(Routing::class) ? Routing::backend() : 'mfw'));
        $namePrefix = RouteNaming::prefix();

        Route::middleware($middleware)
            ->prefix($prefix)
            ->name($namePrefix)
            ->group(__DIR__ . '/Routes/dictionnaries.php');
    }
}

<?php

declare(strict_types=1);

namespace Tests\Concerns;

use MetaFramework\Accessors\Locale as LocaleAccessor;
use ReflectionClass;

trait ResetsMultilangCache
{
    protected function resetMultilangCache(): void
    {
        cache()->forget('mfw.multilang');

        $reflection = new ReflectionClass(LocaleAccessor::class);
        $property = $reflection->getProperty('multilangCache');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}

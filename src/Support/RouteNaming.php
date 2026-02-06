<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Support;

class RouteNaming
{
    public static function prefix(): string
    {
        $prefix = trim((string) config('mfw-dictionnaries.routes.name_prefix', 'mfw.'));

        if ($prefix === '') {
            return '';
        }

        return str_ends_with($prefix, '.') ? $prefix : $prefix . '.';
    }

    public static function name(string $name): string
    {
        return self::prefix() . ltrim($name, '.');
    }
}

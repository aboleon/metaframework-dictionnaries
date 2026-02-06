<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Enum;

use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum DictionnaryType: string implements BackedEnumInteface
{
    case SIMPLE = 'simple';
    case META = 'meta';
    case CUSTOM = 'custom';

    use BackedEnum;

    public static function translationPrefix(): string
    {
        return 'mfw-dictionnaries::mfw-dictionnaries.';
    }

    public static function default(): string
    {
        return self::SIMPLE->value;
    }
}

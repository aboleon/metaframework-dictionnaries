<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Contracts;

interface CustomDictionnaryInterface
{
    public function translatables(): array;

    public function customData(): array;
}

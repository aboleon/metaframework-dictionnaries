<?php

declare(strict_types=1);

namespace Tests\Stubs\DictionnaryEntry;

use MetaFramework\Dictionnaries\Contracts\CustomDictionnaryInterface;

class ServiceFamily implements CustomDictionnaryInterface
{
    public function translatables(): array
    {
        return [
            'subtitle' => [
                'type' => 'text',
                'label' => 'Subtitle',
                'required' => false,
            ],
        ];
    }

    public function customData(): array
    {
        return [
            'icon' => [
                'type' => 'text',
                'label' => 'Icon',
                'required' => false,
            ],
        ];
    }
}

<?php

declare(strict_types=1);

return [
    // Layout bridge used by package pages.
    // Set to an app component (for example: 'backend-layout') if needed.
    'layout_component' => 'mfw-dictionnaries::layout',

    'routes' => [
        'middleware' => ['web'],
        'prefix' => null,
        'name_prefix' => 'mfw.',
    ],

    'entry_subclasses_namespace' => 'App\\Models\\DictionnaryEntry',
];

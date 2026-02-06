# MetaFramework Dictionnaries

`aboleon/metaframework-dictionnaries` is a Laravel package for managing dictionaries and dictionary entries.

It is not restricted to "MetaFramework applications". It can be installed in any Laravel app, while relying on MetaFramework packages as technical dependencies.

It provides:
- Admin routes/controllers/views for dictionaries and entries.
- Support for `simple`, `meta`, and `custom` dictionary types.
- Translation-aware storage and rendering (based on `mfw.translatable.multilang`).
- Cached accessor helpers to read dictionary values in app code.
- Configurable route prefix/name/middleware and configurable backend layout component.

## What The Package Manages

Two entities:
- `Dictionnary` (`dictionnaries` table): defines a dictionary (`slug`, `type`, `name`).
- `DictionnaryEntry` (`dictionnary_entries` table): entries attached to a dictionary, with optional hierarchy (`parent`) and optional extra payload (`custom`).

Dictionary types:
- `simple`: flat entries list.
- `meta`: parent entries with nested sub-entries.
- `custom`: same storage with optional custom translatable/custom fields via subclass extension.

## Requirements

- PHP `^8.3`
- Laravel `^11.0|^12.0`
- `aboleon/metaframework`
- `yajra/laravel-datatables`

`aboleon/metaframework-inputable` and `aboleon/metaframework-support` are pulled transitively by `aboleon/metaframework`.

## Installation

```bash
composer require aboleon/metaframework-dictionnaries
```

Service provider is auto-discovered:
- `MetaFramework\Dictionnaries\DictionnariesServiceProvider`

## Publishables

Publish what you need:

```bash
php artisan vendor:publish --tag=mfw-dictionnaries-config
php artisan vendor:publish --tag=mfw-dictionnaries-migrations
php artisan vendor:publish --tag=mfw-dictionnaries-views
php artisan vendor:publish --tag=mfw-dictionnaries-lang
```

Then run migrations:

```bash
php artisan migrate
```

### Native Vs Published Resources

The package is designed to work in both modes:
- Native package resources (no publish).
- App-copied resources (after `vendor:publish`).

Resolution order:
- Views: `resources/views/vendor/mfw-dictionnaries` first, package views fallback.
- Translations: `lang/vendor/mfw-dictionnaries` first, package translations fallback.

If the package ships public assets in future versions, they can be published with:

```bash
php artisan vendor:publish --tag=mfw-dictionnaries-assets
```

## Configuration

Config file: `config/mfw-dictionnaries.php`

```php
return [
    'layout_component' => 'mfw-dictionnaries::layout',

    'routes' => [
        'middleware' => ['web'],
        'prefix' => null,
        'name_prefix' => 'mfw.',
    ],

    'entry_subclasses_namespace' => 'App\\Models\\DictionnaryEntry',
];
```

### Layout Bridge

Package views do not hardcode `<x-backend-layout>`.
They render through a dynamic component:
- `config('mfw-dictionnaries.layout_component')`

Example using app layout component:

```php
'layout_component' => 'backend-layout',
```

## Routing

Routes are grouped with:
- middleware: `mfw-dictionnaries.routes.middleware`
- prefix: `mfw-dictionnaries.routes.prefix` (fallback to `mfw`)
- name prefix: `mfw-dictionnaries.routes.name_prefix` (default `mfw.`, accepts `panel` or `panel.`)

Main route names:
- `mfw.dictionnary.index`
- `mfw.dictionnary.create`
- `mfw.dictionnary.store`
- `mfw.dictionnary.edit`
- `mfw.dictionnary.update`
- `mfw.dictionnary.destroy`
- `mfw.dictionnary.entries.index`
- `mfw.dictionnary.entries.create`
- `mfw.dictionnary.entries.store`
- `mfw.dictionnaryentry.edit`
- `mfw.dictionnaryentry.update`
- `mfw.dictionnaryentry.destroy`
- `mfw.dictionnaryentry.subentry`
- `mfw.dictionnary.mass-delete`

## Multilang Behavior

Behavior depends on:
- `config('mfw.translatable.multilang')`

Where it is defined (host app):
- `config/mfw.php` under `translatable.multilang` (or equivalent merged `mfw` config).

Example configuration:

```php
// config/mfw.php
return [
    'translatable' => [
        'multilang' => true, // or false
        'locales' => ['fr', 'bg'],
        'active_locales' => ['fr', 'bg'],
    ],
];
```

When `true`:
- Inputs are processed as localized arrays (`name[fr]`, `name[bg]`, ...).
- Sorting for entries uses JSON locale extraction with `app()->getLocale()`.

Request payload example (`multilang = true`):

```php
[
    'name' => [
        'fr' => 'Pays',
        'bg' => 'Държави',
    ],
    'slug' => 'countries',
    'type' => 'simple',
]
```

When `false`:
- Inputs are processed as single values (`name`).
- Sorting uses plain `name` column.
- Models still read legacy JSON-formatted names safely (fallback locale or first available value).

Request payload example (`multilang = false`):

```php
[
    'name' => 'Countries',
    'slug' => 'countries',
    'type' => 'simple',
]
```

Important cache note:
- `MetaFramework\Accessors\Locale::multilang()` is cached (`mfw.multilang`).
- After toggling config, clear cache:

```bash
php artisan cache:forget mfw.multilang
```

## Using The Accessor API

Class: `MetaFramework\Dictionnaries\Accessors\Dictionnaries`

Example:

```php
use MetaFramework\Dictionnaries\Accessors\Dictionnaries;

// Fetch dictionary model by slug (cached forever)
$dictionary = Dictionnaries::dictionnary('countries');

// Flat select values: [id => label]
$values = Dictionnaries::selectValues('countries');

// Meta select values:
// [parentId => ['name' => 'Group', 'values' => [entryId => 'Label']]]
$metaValues = Dictionnaries::selectValues('service_family');

// Helpers
$title = Dictionnaries::title('countries');
$type = Dictionnaries::type('countries'); // simple|meta|custom
$entry = Dictionnaries::entry('countries', 12);

// Clear cache for one dictionary slug
Dictionnaries::reset('countries');
```

## Usage Example In Blade

```blade
<x-mfw-inputable::select
    name="country_id"
    :values="\MetaFramework\Dictionnaries\Accessors\Dictionnaries::selectValues('countries')"
    :affected="old('country_id', $model->country_id)"
/>
```

## Custom Dictionary Entry Subclass

For a dictionary with slug `service_family`, package resolves:
- `App\Models\DictionnaryEntry\ServiceFamily` (or your configured namespace)

Interface:
- `MetaFramework\Dictionnaries\Contracts\CustomDictionnaryInterface`

Example:

```php
<?php

namespace App\Models\DictionnaryEntry;

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
                'label' => 'Icon class',
                'required' => false,
            ],
        ];
    }
}
```

## Translation Files

Package translations are provided for:
- `en`
- `fr`
- `bg`

Namespace:
- `mfw-dictionnaries::mfw-dictionnaries`

Example:

```php
__('mfw-dictionnaries::mfw-dictionnaries.buttons.add')
```

## Acceptance Tests

Acceptance test documentation is available in:
- `docs/acceptance-tests.md`

Quick commands:

```bash
php vendor/bin/phpunit
```

```powershell
$env:XDEBUG_MODE='coverage'; php vendor/bin/phpunit --coverage-clover coverage.xml
```

## Notes

- Naming intentionally follows existing compatibility conventions (`Dictionnary`/`dictionnary`).
- Package is designed to be installed inside an app and adapt to app layout/routing conventions via config.

## License

MIT

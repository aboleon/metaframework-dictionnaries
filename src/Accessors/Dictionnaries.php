<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Accessors;

use Illuminate\Database\Eloquent\Collection;
use MetaFramework\Dictionnaries\Enum\DictionnaryType;
use MetaFramework\Dictionnaries\Models\Dictionnary;
use MetaFramework\Dictionnaries\Models\DictionnaryEntry;
use Throwable;

class Dictionnaries
{
    public static function reset(string $key): void
    {
        cache()->forget('dico-' . $key);
    }

    public static function dictionnary(string $key): ?Dictionnary
    {
        return cache()->rememberForever(
            'dico-' . $key,
            function () use ($key) {
                $dictionnary = Dictionnary::query()->where('slug', $key)->with('entries')->first();

                if ($dictionnary instanceof Dictionnary && $dictionnary->type == DictionnaryType::META->value) {
                    $dictionnary->entries->load('entries');
                }

                return $dictionnary;
            },
        );
    }

    /**
     * @param  string  $key  Dictionary slug.
     * @return array Dictionary entries (with hierarchy if it exists):
     *               simple dictionary: [id => value]
     *               meta dictionary: [id => [name => optgroup, values => [id => value]]]
     */
    public static function selectValues(string $key, array $options = []): array
    {
        $dictionnary = self::dictionnary($key);

        if (!$dictionnary) {
            return [];
        }

        $alphaSort = $options['alphaSort'] ?? false;

        try {
            if ($dictionnary->type === DictionnaryType::META->value) {
                return $dictionnary->entries->sortBy('name')->mapWithKeys(function ($item) {
                    return [
                        $item->id => [
                            'name'   => $item->name,
                            'values' => $item->entries->pluck('name', 'id')->sort()->toArray(),
                        ],
                    ];
                })->toArray();
            }

            $entries = $dictionnary->entries;
            if ($alphaSort) {
                $entries = $entries->sortBy('name');
            }

            return $entries->pluck('name', 'id')->toArray();
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }

    public static function title(string $key): string
    {
        return self::dictionnary($key)?->name
            ?? __('mfw-dictionnaries::mfw-dictionnaries.messages.dictionary_not_found', ['key' => $key]);
    }

    public static function type(string $key): string
    {
        return self::dictionnary($key)->type ?? 'simple';
    }

    public static function entry(string $dictionnary, ?int $entry_key = null): ?DictionnaryEntry
    {
        if (!$entry_key) {
            return null;
        }

        $dict = self::dictionnary($dictionnary);
        if (!$dict) {
            return null;
        }

        // First check top-level entries
        $entry = $dict->entries->firstWhere('id', $entry_key);
        if ($entry) {
            return $entry;
        }

        // If not found and it's a meta dictionary, check nested entries
        if ($dict->type === DictionnaryType::META->value) {
            foreach ($dict->entries as $metaEntry) {
                if ($metaEntry->entries) {
                    $nested = $metaEntry->entries->firstWhere('id', $entry_key);
                    if ($nested) {
                        return $nested;
                    }
                }
            }
        }

        return null;
    }

    public static function filterAgainstMetaType($collection, $array): Collection
    {
        return $collection->filter(function ($item) use ($array) {
            $entries         = collect($item['entries']);
            $matchingEntries = $entries->whereIn('id', $array);

            return !$matchingEntries->isEmpty();
        });
    }

    public static function filterAgainstSimpleType($collection, $array): Collection
    {
        return $collection->filter(fn ($item) => in_array($item->id, $array));
    }
}

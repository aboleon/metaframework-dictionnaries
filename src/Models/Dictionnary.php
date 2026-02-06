<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use MetaFramework\Accessors\Locale as LocaleAccessor;
use MetaFramework\Dictionnaries\Contracts\CustomDictionnaryInterface;
use MetaFramework\Polyglote\Traits\Translation;

/**
 * @property string $slug
 */
class Dictionnary extends Model
{
    use Translation;

    public $timestamps = false;

    public array $fillables = [
        'name' => [
            'type' => 'text',
            'label' => 'mfw-dictionnaries::mfw-dictionnaries.table.headers.name',
            'required' => true,
        ],
    ];

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->translatable = array_keys($this->fillables);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(DictionnaryEntry::class)->whereNull('parent')->whereNull('deleted_at');
    }

    public function getNameAttribute($value): string
    {
        if (LocaleAccessor::multilang()) {
            return (string) $value;
        }

        if (!is_string($value)) {
            return (string) $value;
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return $value;
        }

        $default = config('app.fallback_locale');

        return (string) ($decoded[$default] ?? reset($decoded) ?? '');
    }

    public function entrySubClass(): CustomDictionnaryInterface|bool
    {
        $baseNamespace = trim((string) config('mfw-dictionnaries.entry_subclasses_namespace', 'App\\Models\\DictionnaryEntry'), '\\');
        $subclass = '\\' . $baseNamespace . '\\' . ucfirst(Str::camel($this->slug));

        return class_exists($subclass) ? new $subclass : false;
    }
}

<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFramework\Accessors\Locale as LocaleAccessor;
use MetaFramework\Polyglote\Traits\Translation;

/**
 * @property int|null $parent
 * @property int $dictionnary_id
 */
class DictionnaryEntry extends Model
{
    use SoftDeletes;
    use Translation;

    public $timestamps = false;

    public array $fillables = [
        'name' =>[
            'type' => 'text',
            'label' => 'mfw-dictionnaries::mfw-dictionnaries.table.headers.name',
            'required' => true,
        ],
    ];

    protected $casts = [
        'custom' => 'array',
    ];

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->translatable = array_keys($this->fillables);
    }

    public function scopeOfDictionnary(Builder $query, ?Dictionnary $dictionnary): Builder
    {
        if ($dictionnary?->id) {
            $query->where('dictionnary_id', $dictionnary->id)->with('dictionnary');
        }

        return $query;
    }

    public function dictionnary(): BelongsTo
    {
        return $this->belongsTo(Dictionnary::class);
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

    public function entries(): HasMany
    {
        return $this->hasMany(self::class, 'parent');
    }
}

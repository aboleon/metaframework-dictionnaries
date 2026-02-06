<?php

declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Support\Facades\DB;
use MetaFramework\Dictionnaries\Models\Dictionnary;
use MetaFramework\Dictionnaries\Models\DictionnaryEntry;
use Tests\TestCase;

class ModelBehaviorTest extends TestCase
{
    public function test_it_resolves_entry_subclass_from_configured_namespace(): void
    {
        config()->set('mfw-dictionnaries.entry_subclasses_namespace', 'Tests\\Stubs\\DictionnaryEntry');

        $dictionary = new Dictionnary;
        $dictionary->slug = 'service_family';

        $subclass = $dictionary->entrySubClass();

        $this->assertNotFalse($subclass);
        $this->assertInstanceOf(\Tests\Stubs\DictionnaryEntry\ServiceFamily::class, $subclass);
    }

    public function test_it_reads_legacy_json_name_when_multilang_is_disabled(): void
    {
        config()->set('mfw.translatable.multilang', false);
        config()->set('app.fallback_locale', 'en');
        $this->resetMultilangCache();

        DB::table('dictionnaries')->insert([
            'slug' => 'legacy',
            'type' => 'simple',
            'name' => json_encode(['fr' => 'Pays', 'en' => 'Countries'], JSON_THROW_ON_ERROR),
        ]);

        DB::table('dictionnary_entries')->insert([
            'dictionnary_id' => 1,
            'parent' => null,
            'position' => 0,
            'name' => json_encode(['fr' => 'Belgique', 'en' => 'Belgium'], JSON_THROW_ON_ERROR),
            'custom' => null,
            'deleted_at' => null,
        ]);

        $dictionary = Dictionnary::query()->where('slug', 'legacy')->first();
        $entry = DictionnaryEntry::query()->where('dictionnary_id', 1)->first();

        $this->assertSame('Countries', $dictionary?->name);
        $this->assertSame('Belgium', $entry?->name);
    }
}

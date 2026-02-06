<?php

declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Support\Facades\DB;
use MetaFramework\Dictionnaries\DataTables\DictionnaryDataTable;
use MetaFramework\Dictionnaries\DataTables\View\DictionaryView;
use ReflectionClass;
use Tests\TestCase;

class DictionnaryDataTableViewQueryTest extends TestCase
{
    public function test_it_reads_localized_name_from_view_in_multilang_mode(): void
    {
        config()->set('mfw.translatable.multilang', true);
        config()->set('app.locale', 'fr');
        config()->set('app.fallback_locale', 'en');
        $this->resetMultilangCache();

        DB::table('dictionnaries')->insert([
            'slug' => 'countries',
            'type' => 'simple',
            'name' => json_encode(['en' => 'Countries', 'fr' => 'Pays'], JSON_THROW_ON_ERROR),
        ]);

        DB::table('dictionnary_entries')->insert([
            'dictionnary_id' => 1,
            'parent' => null,
            'position' => 0,
            'name' => json_encode(['en' => 'Belgium', 'fr' => 'Belgique'], JSON_THROW_ON_ERROR),
            'custom' => null,
            'deleted_at' => null,
        ]);

        /** @var DictionnaryDataTable $dataTable */
        $dataTable = (new ReflectionClass(DictionnaryDataTable::class))->newInstanceWithoutConstructor();
        $row = $dataTable->query(new DictionaryView)->first();

        $this->assertNotNull($row);
        $this->assertSame('Pays', $row?->name);
        $this->assertSame(1, (int) $row?->entries_count);
    }

    public function test_it_reads_fallback_or_plain_name_from_view_when_multilang_is_disabled(): void
    {
        config()->set('mfw.translatable.multilang', false);
        config()->set('app.locale', 'fr');
        config()->set('app.fallback_locale', 'en');
        $this->resetMultilangCache();

        DB::table('dictionnaries')->insert([
            'slug' => 'countries',
            'type' => 'simple',
            'name' => json_encode(['en' => 'Countries', 'fr' => 'Pays'], JSON_THROW_ON_ERROR),
        ]);

        DB::table('dictionnaries')->insert([
            'slug' => 'languages',
            'type' => 'simple',
            'name' => 'Languages',
        ]);

        /** @var DictionnaryDataTable $dataTable */
        $dataTable = (new ReflectionClass(DictionnaryDataTable::class))->newInstanceWithoutConstructor();
        $rows = $dataTable->query(new DictionaryView)->orderBy('id')->get();

        $this->assertCount(2, $rows);
        $this->assertSame('Countries', (string) $rows[0]->name);
        $this->assertSame('Languages', (string) $rows[1]->name);
    }
}

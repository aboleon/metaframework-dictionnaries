<?php

declare(strict_types=1);

namespace Tests\Feature;

use MetaFramework\Dictionnaries\Models\Dictionnary;
use Tests\TestCase;

class DictionnaryControllerStoreTest extends TestCase
{
    public function test_it_stores_dictionary_in_multilang_mode(): void
    {
        $response = $this->post(route('mfw.dictionnary.store'), [
            'name' => [
                'en' => 'Countries',
                'fr' => 'Pays',
            ],
            'slug' => 'countries',
            'type' => 'simple',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dictionnaries', [
            'slug' => 'countries',
            'type' => 'simple',
        ]);

        $dictionary = Dictionnary::query()->where('slug', 'countries')->first();
        $this->assertNotNull($dictionary);
        $this->assertSame('Countries', $dictionary?->translation('name', 'en'));
        $this->assertSame('Pays', $dictionary?->translation('name', 'fr'));
    }

    public function test_it_stores_dictionary_in_single_locale_mode(): void
    {
        config()->set('mfw.translatable.multilang', false);
        $this->resetMultilangCache();

        $response = $this->post(route('mfw.dictionnary.store'), [
            'name' => 'Languages',
            'slug' => 'languages',
            'type' => 'simple',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dictionnaries', [
            'slug' => 'languages',
            'type' => 'simple',
        ]);

        $dictionary = Dictionnary::query()->where('slug', 'languages')->first();
        $this->assertNotNull($dictionary);
        $this->assertSame('Languages', $dictionary?->name);
    }
}

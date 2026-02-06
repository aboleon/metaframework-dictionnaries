<?php

declare(strict_types=1);

namespace Tests\Integration;

use MetaFramework\Dictionnaries\Accessors\Dictionnaries;
use MetaFramework\Dictionnaries\Enum\DictionnaryType;
use MetaFramework\Dictionnaries\Models\Dictionnary;
use MetaFramework\Dictionnaries\Models\DictionnaryEntry;
use Tests\TestCase;

class DictionnariesAccessorTest extends TestCase
{
    public function test_it_returns_flat_values_for_simple_dictionary(): void
    {
        $dictionary = new Dictionnary;
        $dictionary->slug = 'countries';
        $dictionary->type = DictionnaryType::SIMPLE->value;
        $dictionary->setTranslations('name', ['en' => 'Countries']);
        $dictionary->save();

        $belgium = new DictionnaryEntry;
        $belgium->dictionnary_id = $dictionary->id;
        $belgium->setTranslations('name', ['en' => 'Belgium']);
        $belgium->save();

        $france = new DictionnaryEntry;
        $france->dictionnary_id = $dictionary->id;
        $france->setTranslations('name', ['en' => 'France']);
        $france->save();

        Dictionnaries::reset('countries');
        $values = Dictionnaries::selectValues('countries', ['alphaSort' => true]);

        $this->assertSame('Belgium', $values[$belgium->id]);
        $this->assertSame('France', $values[$france->id]);
    }

    public function test_it_returns_nested_values_and_nested_entry_for_meta_dictionary(): void
    {
        $dictionary = new Dictionnary;
        $dictionary->slug = 'service_family';
        $dictionary->type = DictionnaryType::META->value;
        $dictionary->setTranslations('name', ['en' => 'Service Families']);
        $dictionary->save();

        $parent = new DictionnaryEntry;
        $parent->dictionnary_id = $dictionary->id;
        $parent->setTranslations('name', ['en' => 'Medical']);
        $parent->save();

        $child = new DictionnaryEntry;
        $child->dictionnary_id = $dictionary->id;
        $child->parent = $parent->id;
        $child->setTranslations('name', ['en' => 'Cardiology']);
        $child->save();

        Dictionnaries::reset('service_family');
        $values = Dictionnaries::selectValues('service_family');
        $entry = Dictionnaries::entry('service_family', $child->id);

        $this->assertArrayHasKey($parent->id, $values);
        $this->assertSame('Medical', $values[$parent->id]['name']);
        $this->assertSame('Cardiology', $values[$parent->id]['values'][$child->id]);
        $this->assertNotNull($entry);
        $this->assertSame($child->id, $entry?->id);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
    public function test_it_registers_main_dictionary_routes_with_default_prefix(): void
    {
        $this->assertTrue(Route::has('mfw.dictionnary.index'));
        $this->assertTrue(Route::has('mfw.dictionnary.store'));
        $this->assertTrue(Route::has('mfw.dictionnary.entries.index'));
        $this->assertTrue(Route::has('mfw.dictionnaryentry.subentry'));
        $this->assertTrue(Route::has('mfw.dictionnaryentry.subentrty'));
        $this->assertTrue(Route::has('mfw.dictionnary.mass-delete'));

        $this->assertSame('mfw/dictionnary', Route::getRoutes()->getByName('mfw.dictionnary.index')?->uri());
        $this->assertSame('mfw/dictionnary/{dictionnary}/entries', Route::getRoutes()->getByName('mfw.dictionnary.entries.index')?->uri());
        $this->assertSame('mfw/dictionnaryentry/subentry/{dictionnaryentry}', Route::getRoutes()->getByName('mfw.dictionnaryentry.subentry')?->uri());
    }
}

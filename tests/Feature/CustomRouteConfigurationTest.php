<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use MetaFramework\Dictionnaries\Models\Dictionnary;
use MetaFramework\Dictionnaries\Support\RouteNaming;
use Tests\TestCase;

class CustomRouteConfigurationTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('mfw-dictionnaries.routes.prefix', 'admin-dico');
        $app['config']->set('mfw-dictionnaries.routes.name_prefix', 'panel');
    }

    public function test_it_applies_custom_prefix_and_name_prefix(): void
    {
        $this->assertTrue(Route::has('panel.dictionnary.index'));
        $this->assertTrue(Route::has('panel.dictionnary.entries.index'));
        $this->assertTrue(Route::has('panel.dictionnaryentry.subentry'));

        $this->assertSame('admin-dico/dictionnary', Route::getRoutes()->getByName('panel.dictionnary.index')?->uri());
        $this->assertSame('admin-dico/dictionnary/{dictionnary}/entries', Route::getRoutes()->getByName('panel.dictionnary.entries.index')?->uri());
    }

    public function test_it_uses_custom_named_routes_in_store_redirect(): void
    {
        $this->assertSame('panel.dictionnary.store', RouteNaming::name('dictionnary.store'));
        $this->assertSame('panel.dictionnary.edit', RouteNaming::name('dictionnary.edit'));

        $storeResponse = $this->post(route('panel.dictionnary.store'), [
            'name' => ['en' => 'Channels'],
            'slug' => 'channels',
            'type' => 'simple',
        ]);

        $dictionary = Dictionnary::query()->where('slug', 'channels')->first();
        $this->assertNotNull($dictionary);
        $storeResponse->assertRedirect(route('panel.dictionnary.edit', $dictionary));
    }
}

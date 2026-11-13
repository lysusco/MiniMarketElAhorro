<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PanelTest extends DuskTestCase
{
    /**
     * El panel muestra los indicadores principales del minimercado.
     */
    public function test_panel_muestra_indicadores(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs(\App\Models\User::where('email', 'admin@minimercado.com')->first())
                ->visit('/dashboard')
                ->assertSee('Panel principal')
                ->assertSee('Productos activos')
                ->assertSee('Stock bajo')
                ->assertSee('Ventas de hoy')
                ->assertSee('Cartera pendiente');
        });
    }
}

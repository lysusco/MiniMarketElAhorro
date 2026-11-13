<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PermisosTest extends DuskTestCase
{
    /**
     * Un empleado no puede gestionar categorías: el servidor responde 403.
     */
    public function test_empleado_no_puede_gestionar_categorias(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                ->type('email', 'empleado@minimercado.com')
                ->type('password', 'empleado123')
                ->waitFor('button[type="submit"]')->click('button[type="submit"]')
                ->visit('/categorias')
                ->assertSee('Acceso no autorizado');
        });
    }
}

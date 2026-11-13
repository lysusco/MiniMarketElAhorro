<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * El administrador inicia sesión y llega al panel principal.
     */
    public function test_admin_inicia_sesion_y_ve_el_panel(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                ->type('email', 'admin@minimercado.com')
                ->type('password', 'admin123')
                ->waitFor('button[type="submit"]')->click('button[type="submit"]')
                ->assertPathIs('/dashboard')
                ->assertSee('Panel principal');
        });
    }

    /**
     * El empleado inicia sesión y llega al panel principal.
     */
    public function test_empleado_inicia_sesion_y_ve_el_panel(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                ->type('email', 'empleado@minimercado.com')
                ->type('password', 'empleado123')
                ->waitFor('button[type="submit"]')->click('button[type="submit"]')
                ->assertPathIs('/dashboard')
                ->assertSee('Panel principal');
        });
    }

    /**
     * Con credenciales inválidas se muestra un mensaje de error en español.
     */
    public function test_login_con_credenciales_invalidas_muestra_error(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                ->type('email', 'admin@minimercado.com')
                ->type('password', 'contraseña-incorrecta')
                ->waitFor('button[type="submit"]')->click('button[type="submit"]')
                ->assertPathIs('/login');
        });
    }
}

<?php

namespace Tests\Browser;

use App\Models\Categoria;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CategoriasBrowserTest extends DuskTestCase
{
    /**
     * El administrador crea una categoría y la ve en el listado.
     */
    public function test_admin_crea_categoria(): void
    {
        $nombre = 'Categoria Dusk ' . uniqid();

        $this->browse(function (Browser $browser) use ($nombre) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs(\App\Models\User::where('email', 'admin@minimercado.com')->first())
                ->visit('/categorias/create')
                ->type('nombre', $nombre)
                ->type('descripcion', 'Creada durante la prueba de navegador')
                ->waitFor('button[type="submit"]')->click('button[type="submit"]')
                ->assertPathIs('/categorias')
                ->assertSee('Categoría creada correctamente.')
                ->assertSee($nombre);
        });

        // Limpieza: se elimina la categoría creada para no contaminar los datos.
        Categoria::where('nombre', $nombre)->delete();
    }

    /**
     * El administrador edita una categoría existente.
     */
    public function test_admin_edita_categoria(): void
    {
        $categoria = Categoria::create([
            'nombre' => 'Categoria Editar Dusk ' . uniqid(),
            'activo' => true,
        ]);

        $nuevoNombre = 'Categoria Editada Dusk ' . uniqid();

        $this->browse(function (Browser $browser) use ($categoria, $nuevoNombre) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs(\App\Models\User::where('email', 'admin@minimercado.com')->first())
                ->visit('/categorias/' . $categoria->id . '/edit')
                ->clear('nombre')
                ->type('nombre', $nuevoNombre)
                ->waitFor('button[type="submit"]')->click('button[type="submit"]')
                ->assertPathIs('/categorias')
                ->assertSee('Categoría actualizada correctamente.')
                ->assertSee($nuevoNombre);
        });

        Categoria::where('id', $categoria->id)->delete();
    }

    /**
     * El administrador elimina una categoría sin productos relacionados.
     */
    public function test_admin_elimina_categoria(): void
    {
        $categoria = Categoria::create([
            'nombre' => 'Categoria Eliminar Dusk ' . uniqid(),
            'activo' => true,
        ]);

        $this->browse(function (Browser $browser) use ($categoria) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs(\App\Models\User::where('email', 'admin@minimercado.com')->first())
                ->visit('/categorias')
                ->within("form[action$='/categorias/{$categoria->id}']", function (Browser $browser) {
                    $browser->waitFor('button[type="submit"]')->click('button[type="submit"]');
                })
                ->acceptDialog()
                ->assertPathIs('/categorias')
                ->assertDontSee($categoria->nombre);
        });

        // Si por alguna razón quedó, se fuerza la limpieza.
        Categoria::where('id', $categoria->id)->delete();
    }
}

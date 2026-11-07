<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_se_muestra_la_pagina_de_inicio_de_sesion(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_un_usuario_puede_iniciar_sesion_con_credenciales_validas(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_no_se_puede_iniciar_sesion_con_credenciales_invalidas(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
            'role' => User::ROLE_EMPLEADO,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'contraseña-incorrecta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_visitante_no_autenticado_no_puede_usar_el_sistema(): void
    {
        // Toda operación exige iniciar sesión antes de continuar.
        $this->get('/')->assertRedirect(route('dashboard'));
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/categorias')->assertRedirect('/login');
    }

    public function test_no_existe_el_registro_publico_de_usuarios(): void
    {
        // RF-03: las cuentas solo las gestiona el administrador.
        $this->get('/register')->assertNotFound();
    }

    public function test_un_usuario_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_EMPLEADO,
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}

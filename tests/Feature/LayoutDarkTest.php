<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutDarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_usa_layout_oscuro(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('bg-bg-100');
    }

    public function test_login_usa_layout_oscuro(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('bg-bg-100');
    }
}

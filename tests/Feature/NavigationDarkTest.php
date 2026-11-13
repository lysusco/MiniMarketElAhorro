<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationDarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_usa_tema_oscuro(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }
}

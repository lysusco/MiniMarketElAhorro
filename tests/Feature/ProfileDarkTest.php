<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileDarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_usa_tema_oscuro(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }
}

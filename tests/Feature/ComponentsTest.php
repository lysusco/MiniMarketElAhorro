<?php

namespace Tests\Feature;

use Tests\TestCase;

class ComponentsTest extends TestCase
{
    public function test_button_primary_renders_token(): void
    {
        $view = $this->blade('<x-button-primary>Hola</x-button-primary>');
        $view->assertSee('bg-primary-100');
        $view->assertSee('Hola');
    }

    public function test_button_secondary_renders(): void
    {
        $view = $this->blade('<x-button-secondary>Hola</x-button-secondary>');
        $view->assertSee('bg-bg-200');
        $view->assertSee('Hola');
    }

    public function test_button_ghost_renders(): void
    {
        $view = $this->blade('<x-button-ghost>Hola</x-button-ghost>');
        $view->assertSee('bg-transparent');
        $view->assertSee('Hola');
    }

    public function test_button_danger_renders(): void
    {
        $view = $this->blade('<x-button-danger>Hola</x-button-danger>');
        $view->assertSee('bg-red-600');
        $view->assertSee('Hola');
    }

    public function test_badge_renders(): void
    {
        $view = $this->blade('<x-badge>OK</x-badge>');
        $view->assertSee('OK');
    }

    public function test_card_renders(): void
    {
        $view = $this->blade('<x-card>Hola</x-card>');
        $view->assertSee('bg-bg-200');
        $view->assertSee('Hola');
    }

    public function test_alert_renders(): void
    {
        $view = $this->blade('<x-alert>Hola</x-alert>');
        $view->assertSee('Hola');
    }

}

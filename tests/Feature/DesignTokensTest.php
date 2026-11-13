<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignTokensTest extends TestCase
{
    public function test_tailwind_config_defines_design_tokens(): void
    {
        $config = file_get_contents(base_path('tailwind.config.js'));
        $this->assertStringContainsString("'bg'", $config);
        $this->assertStringContainsString("'#0F1C2E'", $config);
        $this->assertStringContainsString("'primary'", $config);
        $this->assertStringContainsString("'accent'", $config);
        $this->assertStringContainsString("'text'", $config);
    }

    public function test_app_css_defines_root_variables(): void
    {
        $css = file_get_contents(base_path('resources/css/app.css'));
        $this->assertStringContainsString(':root', $css);
        $this->assertStringContainsString('--bg-100', $css);
        $this->assertStringContainsString('#0F1C2E', $css);
    }
}

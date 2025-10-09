<?php

namespace Tests\Unit;

use App\Livewire\DashboardGlobalOverview;
use Tests\TestCase;

class DashboardGlobalOverviewTest extends TestCase
{
    public function test_component_renders()
    {
        // Test if component renders without error
        $component = new DashboardGlobalOverview();
        $view = $component->render();

        $this->assertNotNull($view);
        $this->assertIsObject($view);
    }
}

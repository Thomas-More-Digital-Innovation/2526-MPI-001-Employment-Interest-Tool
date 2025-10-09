<?php

namespace Tests\Unit;

use App\Livewire\DashboardGlobalOverview;
use Illuminate\View\View;
use Tests\TestCase;

class DashboardGlobalOverviewTest extends TestCase
{
    public function test_render_returns_view_with_variables()
    {
        $component = new DashboardGlobalOverview();
        $view = $component->render();

        $this->assertInstanceOf(View::class, $view);

        $this->assertEquals('livewire.dashboard-global-overview', $view->getName());

        $data = $view->getData();
        $this->assertArrayHasKey('totalOrganisations', $data);
        $this->assertArrayHasKey('totalUsers', $data);
        $this->assertArrayHasKey('totalTests', $data);
        $this->assertArrayHasKey('completionScore', $data);
        $this->assertArrayHasKey('interestFields', $data);
        $this->assertArrayHasKey('mostChosenIntrestFields', $data);
    }
}

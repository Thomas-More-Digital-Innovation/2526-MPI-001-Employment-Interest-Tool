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

        //check if render give a view back
        $this->assertInstanceOf(View::class, $view);

        //check if view-variabelen exist
        $data = $view->getData();
        $this->assertArrayHasKey('totalOrganisations', $data);
        $this->assertArrayHasKey('totalUsers', $data);
        $this->assertArrayHasKey('totalTests', $data);
        $this->assertArrayHasKey('completionScore', $data);
        $this->assertArrayHasKey('interestFields', $data);
        $this->assertArrayHasKey('mostChosenIntrestFields', $data);

        //check if view has correct name
        $this->assertEquals('livewire.dashboard-global-overview', $view->getName());
    }
}

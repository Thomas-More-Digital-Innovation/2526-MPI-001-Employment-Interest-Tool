<?php

namespace Tests\Unit;

use App\Livewire\DashboardGlobalOverview;
use Illuminate\View\View;
use Tests\TestCase;

class DashboardGlobalOverviewTest extends TestCase
{
    public function test_view_receives_expected_variables()
    {
        $component = new DashboardGlobalOverview();
        $view = $component->render();

        // Controleer dat render een view object teruggeeft
        $this->assertInstanceOf(View::class, $view);

        // Verkrijg alle data uit de view
        $data = $view->getData();

        // Controleer dat de verwachte variabelen bestaan
        $this->assertArrayHasKey('totalOrganisations', $data);
        $this->assertArrayHasKey('totalUsers', $data);
        $this->assertArrayHasKey('totalTests', $data);
        $this->assertArrayHasKey('completionScore', $data);
        $this->assertArrayHasKey('interestFields', $data);
        $this->assertArrayHasKey('mostChosenIntrestFields', $data);
    }
}

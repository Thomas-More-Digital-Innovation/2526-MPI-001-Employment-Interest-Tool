<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Livewire\Mentor\ClientManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientManagementUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_successfully()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientManagement::class);

        // Assert: Component renders successfully
        $component->assertOk();
    }

    public function test_component_handles_client_created_event()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Dispatch client-created event
        $component = Livewire::test(ClientManagement::class)
            ->dispatch('client-created');

        // Assert: Refresh key is incremented and success message is dispatched
        $component->assertSet('refreshKey', 1)
                 ->assertDispatched('success', 'Client created successfully.');
    }

    public function test_component_handles_client_updated_event()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Dispatch client-updated event
        $component = Livewire::test(ClientManagement::class)
            ->dispatch('client-updated');

        // Assert: Refresh key is incremented and success message is dispatched
        $component->assertSet('refreshKey', 1)
                 ->assertDispatched('success', 'Client updated successfully.');
    }

    public function test_component_handles_client_deactivated_event()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Dispatch client-deactivated event
        $component = Livewire::test(ClientManagement::class)
            ->dispatch('client-deactivated');

        // Assert: Refresh key is incremented and success message is dispatched
        $component->assertSet('refreshKey', 1)
                 ->assertDispatched('success', 'Client deactivated successfully.');
    }

    public function test_component_handles_client_restored_event()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Dispatch client-restored event
        $component = Livewire::test(ClientManagement::class)
            ->dispatch('client-restored');

        // Assert: Refresh key is incremented and success message is dispatched
        $component->assertSet('refreshKey', 1)
                 ->assertDispatched('success', 'Client restored successfully.');
    }

    public function test_component_initializes_with_zero_refresh_key()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientManagement::class);

        // Assert: Refresh key starts at 0
        $component->assertSet('refreshKey', 0);
    }

    public function test_component_has_correct_event_listeners()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Create the component instance
        $component = new \App\Livewire\Mentor\ClientManagement();
        
        // Get the protected listeners property using Reflection
        $reflection = new \ReflectionClass($component);
        $listenersProperty = $reflection->getProperty('listeners');
        $listenersProperty->setAccessible(true);
        $listeners = $listenersProperty->getValue($component);

        // Assert: Component has the correct event listeners
        $this->assertArrayHasKey('client-created', $listeners);
        $this->assertArrayHasKey('client-updated', $listeners);
        $this->assertArrayHasKey('client-deactivated', $listeners);
        $this->assertArrayHasKey('client-restored', $listeners);
    }

    public function test_multiple_events_increment_refresh_key_correctly()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Dispatch multiple events
        $component = Livewire::test(ClientManagement::class)
            ->dispatch('client-created')
            ->dispatch('client-updated')
            ->dispatch('client-deactivated');

        // Assert: Refresh key is incremented for each event
        $component->assertSet('refreshKey', 3);
    }
}

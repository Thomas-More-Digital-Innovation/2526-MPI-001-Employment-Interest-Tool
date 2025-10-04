<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Livewire\Mentor\ClientDeleteModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\ManagesTestData;

class ClientDeleteModalUnitTest extends TestCase
{
    use RefreshDatabase, ManagesTestData;

    public function test_component_renders_modal()
    {
        // Arrange: Create a mentor user with required data
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientDeleteModal::class);

        // Assert: Component renders successfully
        $component->assertOk();
    }

    public function test_open_modal_sets_client_id_and_shows_modal()
    {
        // Arrange: Create mentor and client
        $mentor = User::factory()->create();
        $client = User::factory()->create(['mentor_id' => $mentor->user_id]);

        $this->actingAs($mentor);

        // Act: Open modal for client
        $component = Livewire::test(ClientDeleteModal::class)
            ->call('openModal', $client->user_id);

        // Assert: Modal is shown and client ID is set
        $component->assertSet('clientId', $client->user_id)
                 ->assertSet('showModal', true);
    }

    public function test_open_modal_only_allows_own_clients()
    {
        // Arrange: Create two mentors and a client belonging to mentor2
        $mentor1 = $this->createMentorUser();
        $mentor2 = $this->createMentorUser();
        $client = $this->createClientUser($mentor2);
        
        $this->actingAs($mentor1);

        // Act: Try to open modal for mentor2's client
        $component = Livewire::test(ClientDeleteModal::class)
            ->call('openModal', $client->user_id);

        // Assert: Modal doesn't open and error is dispatched
        $component->assertSet('showModal', false)
                 ->assertDispatched('error');
    }

    public function test_delete_client_deactivates_client()
    {
        // Arrange: Create mentor and active client
        $mentor = $this->createMentorUser();
        $client = $this->createClientUser($mentor, ['active' => true]);
        
        $this->actingAs($mentor);

        // Act: Delete (deactivate) client
        $component = Livewire::test(ClientDeleteModal::class)
            ->set('clientId', $client->user_id)
            ->call('deleteClient');

        // Assert: Client is deactivated
        $this->assertDatabaseHas('users', [
            'user_id' => $client->user_id,
            'active' => false,
        ]);
        
        $component->assertSet('clientId', null);
        
        // Verify client is deactivated in database
        $client->refresh();
        $this->assertFalse($client->active);
    }

    public function test_close_modal_resets_state()
    {
        // Arrange: Create a mentor user
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Open and close modal
        $component = Livewire::test(ClientDeleteModal::class)
            ->set('clientId', 1)
            ->set('showModal', true)
            ->call('closeModal');

        // Assert: Modal is closed and client ID is reset
        $component->assertSet('showModal', false)
                 ->assertSet('clientId', null);
    }

    public function test_delete_client_dispatches_success_event()
    {
        // Arrange: Create mentor and active client
        $mentor = $this->createMentorUser();
        $client = $this->createClientUser($mentor, ['active' => true]);
        
        $this->actingAs($mentor);

        // Act: Delete client and check for dispatched event
        $component = Livewire::test(ClientDeleteModal::class)
            ->set('clientId', $client->user_id)
            ->call('deleteClient')
            ->assertDispatched('client-deactivated');
    }

    public function test_delete_client_through_modal_flow()
    {
        // Arrange: Create mentor and active client
        $mentor = $this->createMentorUser();
        $client = $this->createClientUser($mentor, ['active' => true]);
        
        $this->actingAs($mentor);

        // Act: Test the complete modal flow
        $component = Livewire::test(ClientDeleteModal::class)
            ->call('openModal', $client->user_id)
            ->call('deleteClient')
            ->assertDispatched('client-deactivated');
            
        // Assert: Client is deactivated
        $this->assertDatabaseHas('users', [
            'user_id' => $client->user_id,
            'active' => false,
        ]);
    }
}

<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Livewire\Mentor\ClientRestoreModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientRestoreModalUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_modal()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientRestoreModal::class);

        // Assert: Component renders successfully
        $component->assertOk();
    }

    public function test_open_modal_sets_client_id_and_shows_modal()
    {
        // Arrange: Create mentor and inactive client
        $mentor = User::factory()->create();
        $client = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'active' => false
        ]);

        $this->actingAs($mentor);

        // Act: Open modal for client
        $component = Livewire::test(ClientRestoreModal::class)
            ->call('openModal', $client->user_id);

        // Assert: Modal is shown and client ID is set
        $component->assertSet('clientId', $client->user_id)
                 ->assertSet('showModal', true);
    }

    public function test_open_modal_only_allows_own_clients()
    {
        // Arrange: Create two mentors and a client belonging to mentor2
        $mentor1 = User::factory()->create();
        $mentor2 = User::factory()->create();

        $client = User::factory()->create([
            'mentor_id' => $mentor2->user_id,
            'active' => false
        ]);

        $this->actingAs($mentor1);

        // Act: Try to open modal for mentor2's client
        $component = Livewire::test(ClientRestoreModal::class)
            ->call('openModal', $client->user_id);

        // Assert: Modal doesn't open and error is dispatched
        $component->assertSet('showModal', false)
                 ->assertDispatched('error');
    }

    public function test_restore_client_activates_client()
    {
        // Arrange: Create mentor and inactive client
        $mentor = User::factory()->create();
        $client = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'active' => false
        ]);

        $clientRole = Role::where('role', Role::CLIENT)->first();
        $client->roles()->attach($clientRole->role_id);

        $this->actingAs($mentor);

        // Act: Restore (activate) client
        $component = Livewire::test(ClientRestoreModal::class)
            ->call('openModal', $client->user_id)
            ->call('restoreClient');

        // Assert: Client is activated and modal is closed
        $component->assertSet('showModal', false)
                 ->assertSet('clientId', null);

        // Verify client is activated in database
        $client->refresh();
        $this->assertTrue($client->active);
    }

    public function test_restore_client_only_allows_own_clients()
    {
        // Arrange: Create two mentors with roles and a client belonging to mentor2
        $mentor1 = User::factory()->create();
        $mentor2 = User::factory()->create();
        
        // Create roles if they don't exist
        $mentorRole = \App\Models\Role::firstOrCreate(['role' => 'Mentor']);
        $clientRole = \App\Models\Role::firstOrCreate(['role' => 'Client']);
        
        // Assign roles
        $mentor1->roles()->attach($mentorRole->role_id);
        $mentor2->roles()->attach($mentorRole->role_id);
        
        // Create an inactive client for mentor2
        $client = User::factory()->create([
            'mentor_id' => $mentor2->user_id,
            'active' => false
        ]);
        $client->roles()->attach($clientRole->role_id);

        $this->actingAs($mentor1);

        // Act & Assert: Try to restore mentor2's client - should throw ModelNotFoundException
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $component = Livewire::test(ClientRestoreModal::class);
        $component->call('openModal', $client->user_id);
        
        // This should throw an exception because the client doesn't belong to mentor1
        $component->call('restoreClient');
    }

    public function test_close_modal_resets_state()
    {
        // Arrange: Create mentor and client
        $mentor = User::factory()->create();
        $client = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'active' => false
        ]);

        $this->actingAs($mentor);

        // Act: Open modal and close it
        $component = Livewire::test(ClientRestoreModal::class)
            ->call('openModal', $client->user_id)
            ->call('closeModal');

        // Assert: Modal is closed and state is reset
        $component->assertSet('showModal', false)
                 ->assertSet('clientId', null);
    }

    public function test_restore_client_dispatches_success_event()
    {
        // Arrange: Create mentor and inactive client
        $mentor = User::factory()->create();
        $client = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'active' => false
        ]);

        $this->actingAs($mentor);

        // Act: Restore client
        $component = Livewire::test(ClientRestoreModal::class)
            ->call('openModal', $client->user_id)
            ->call('restoreClient');

        // Assert: Success event is dispatched
        $component->assertDispatched('client-restored');
    }
}

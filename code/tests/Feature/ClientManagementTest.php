<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Tests\TestCase;
use Tests\Concerns\InteractsWithRoles;
use Livewire\Livewire;
use App\Livewire\Mentor\ClientManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    /** @test */
    public function it_shows_clients_assigned_to_mentor()
    {
        // Create a mentor with 3 clients
        [$mentor, $clients] = $this->createMentorWithClients(3);
        
        // Create another mentor with their own client (shouldn't be visible)
        $otherMentor = $this->createUserWithRole(Role::MENTOR);
        $otherClient = $this->createUserWithRole(Role::CLIENT, ['mentor_id' => $otherMentor->user_id]);

        $this->actingAs($mentor);

        // Act: Access the client management page
        $response = $this->get(route('mentor.clients'));
        
        // Assert: Page loads successfully
        $response->assertStatus(200);
        
        // Assert: Only the mentor's clients are shown
        foreach ($clients as $client) {
            $response->assertSee($client->full_name);
        }
        
        // Assert: Other mentor's client is not shown
        $response->assertDontSee($otherClient->full_name);
    }

    /** @test */
    public function it_allows_creating_new_clients()
    {
        $mentor = $this->createUserWithRole(Role::MENTOR);
        
        $this->actingAs($mentor);

        $clientData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'username' => 'johndoe',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_sound_on' => true,
            'vision_type' => 'normal',
        ];

        // Act: Create a new client
        Livewire::test(ClientManagement::class)
            ->call('openCreateModal')
            ->set('createForm', $clientData)
            ->call('createClient');

        // Assert: Client was created and assigned to mentor
        $this->assertDatabaseHas('user', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'mentor_id' => $mentor->user_id,
            'active' => true,
        ]);

        // Assert: Client has the client role
        $client = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue($client->hasRole(Role::CLIENT));
    }

    /** @test */
    public function it_validates_client_creation()
    {
        $mentor = $this->createUserWithRole(Role::MENTOR);
        $this->actingAs($mentor);

        // Act & Assert: Submit empty form
        Livewire::test(ClientManagement::class)
            ->call('openCreateModal')
            ->call('createClient')
            ->assertHasErrors([
                'createForm.first_name' => 'required',
                'createForm.last_name' => 'required',
                'createForm.email' => 'required',
                'createForm.username' => 'required',
                'createForm.password' => 'required',
            ]);
    }

    /** @test */
    public function it_allows_editing_clients()
    {
        // Create mentor with a client
        [$mentor, [$client]] = $this->createMentorWithClients(1);
        
        $this->actingAs($mentor);

        $updatedData = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
            'username' => 'updatedusername',
            'is_sound_on' => false,
            'vision_type' => 'colorblind',
        ];

        // Act: Update client
        Livewire::test(ClientManagement::class)
            ->call('openEditModal', $client->user_id)
            ->set('editForm', $updatedData)
            ->call('updateClient');

        // Assert: Client was updated
        $this->assertDatabaseHas('user', [
            'user_id' => $client->user_id,
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function it_allows_deactivating_clients()
    {
        // Create mentor with an active client
        [$mentor, [$client]] = $this->createMentorWithClients(1);
        
        $this->actingAs($mentor);

        // Act: Deactivate client
        Livewire::test(ClientManagement::class)
            ->call('openDeleteModal', $client->user_id)
            ->call('deleteClient');

        // Assert: Client was deactivated
        $this->assertDatabaseHas('user', [
            'user_id' => $client->user_id,
            'active' => false,
        ]);
    }

    /** @test */
    public function it_allows_reactivating_clients()
    {
        // Create mentor with an inactive client
        $mentor = $this->createUserWithRole(Role::MENTOR);
        $client = $this->createUserWithRole(Role::CLIENT, [
            'mentor_id' => $mentor->user_id,
            'active' => false,
        ]);
        
        $this->actingAs($mentor);

        // Act: Reactivate client
        Livewire::test(ClientManagement::class)
            ->call('openRestoreModal', $client->user_id)
            ->call('restoreClient');

        // Assert: Client was reactivated
        $this->assertDatabaseHas('user', [
            'user_id' => $client->user_id,
            'active' => true,
        ]);
    }
}

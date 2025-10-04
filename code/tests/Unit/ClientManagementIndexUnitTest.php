<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Language;
use App\Models\Role;
use App\Livewire\Mentor\ClientManagementIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientManagementIndexUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_with_empty_search()
    {
        // Arrange: Create a mentor user
        $mentor = User::factory()->create();
        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientManagementIndex::class);

        // Assert: Component renders successfully
        $component->assertOk();
    }

    public function test_component_shows_clients_assigned_to_mentor()
    {
        // Arrange: Create mentor and client
        $mentor = User::factory()->create();
        $client = User::factory()->create(['mentor_id' => $mentor->user_id]);

        // Assign client role
        $clientRole = Role::where('role', Role::CLIENT)->first();
        $client->roles()->attach($clientRole->role_id);

        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientManagementIndex::class);

        // Assert: Client is displayed
        $component->assertSee($client->first_name)
                 ->assertSee($client->last_name)
                 ->assertSee($client->username);
    }

    public function test_search_functionality_works()
    {
        // Arrange: Create mentor and multiple clients
        $mentor = User::factory()->create();
        $client1 = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        $client2 = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'first_name' => 'Jane',
            'last_name' => 'Smith'
        ]);

        // Assign client roles
        $clientRole = Role::where('role', Role::CLIENT)->first();
        $client1->roles()->attach($clientRole->role_id);
        $client2->roles()->attach($clientRole->role_id);

        $this->actingAs($mentor);

        // Act: Search for "John"
        $component = Livewire::test(ClientManagementIndex::class)
            ->set('search', 'John');

        // Assert: Only John is shown
        $component->assertSee('John')
                 ->assertSee('Doe')
                 ->assertDontSee('Jane')
                 ->assertDontSee('Smith');
    }

    public function test_reset_search_clears_search_and_resets_page()
    {
        // Arrange: Create mentor and client
        $mentor = User::factory()->create();
        $client = User::factory()->create(['mentor_id' => $mentor->user_id]);

        $clientRole = Role::where('role', Role::CLIENT)->first();
        $client->roles()->attach($clientRole->role_id);

        $this->actingAs($mentor);

        // Act: Set search and reset it
        $component = Livewire::test(ClientManagementIndex::class)
            ->set('search', 'test')
            ->call('resetSearch');

        // Assert: Search is cleared
        $component->assertSet('search', '');
    }

    public function test_component_shows_active_and_inactive_badges_correctly()
    {
        // Arrange: Create mentor and clients with different statuses
        $mentor = User::factory()->create();
        $activeClient = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'active' => true
        ]);
        $inactiveClient = User::factory()->create([
            'mentor_id' => $mentor->user_id,
            'active' => false
        ]);

        // Assign client roles
        $clientRole = Role::where('role', Role::CLIENT)->first();
        $activeClient->roles()->attach($clientRole->role_id);
        $inactiveClient->roles()->attach($clientRole->role_id);

        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientManagementIndex::class);

        // Assert: Correct badges are shown
        $component->assertSee('Active')
                 ->assertSee('Inactive');
    }

    public function test_component_only_shows_clients_assigned_to_current_mentor()
    {
        // Arrange: Create two mentors and their clients
        $mentor1 = User::factory()->create();
        $mentor2 = User::factory()->create();

        $client1 = User::factory()->create(['mentor_id' => $mentor1->user_id]);
        $client2 = User::factory()->create(['mentor_id' => $mentor2->user_id]);

        // Assign client roles
        $clientRole = Role::where('role', Role::CLIENT)->first();
        $client1->roles()->attach($clientRole->role_id);
        $client2->roles()->attach($clientRole->role_id);

        $this->actingAs($mentor1);

        // Act: Render component as mentor1
        $component = Livewire::test(ClientManagementIndex::class);

        // Assert: Only mentor1's client is shown
        $component->assertSee($client1->first_name)
                 ->assertDontSee($client2->first_name);
    }
}

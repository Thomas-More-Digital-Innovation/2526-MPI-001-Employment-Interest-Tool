<?php

namespace Tests\Feature\Mentor;

use App\Livewire\Mentor\ClientsManager;
use App\Models\Option;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ClientsManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $mentor;
    protected Role $clientRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mentor = User::factory()->create([
            'first_name' => 'Mentor',
            'last_name' => 'User',
            'username' => 'mentor_user',
            'active' => true,
        ]);

        $this->clientRole = Role::factory()->create([
            'role' => Role::CLIENT,
        ]);

        $mentorRole = Role::factory()->create([
            'role' => Role::MENTOR,
        ]);

        $this->mentor->roles()->attach($mentorRole->role_id);
    }

    /** @test */
    public function test_mentor_can_create_client_with_disabilities(): void
    {
        $option = Option::factory()->create([
            'type' => Option::TYPE_DISABILITY,
        ]);

        Livewire::actingAs($this->mentor)
            ->test(ClientsManager::class)
            ->call('startCreate')
            ->set('form.first_name', 'New')
            ->set('form.last_name', 'Client')
            ->set('form.username', 'new_client')
            ->set('form.password', 'Password123!')
            ->set('form.language_id', $this->mentor->language_id)
            ->set('form.disability_ids', [$option->option_id])
            ->set('form.active', true)
            ->call('save')
            ->assertDispatched('crud-record-saved');

        $this->assertDatabaseHas('users', [
            'username' => 'new_client',
            'mentor_id' => $this->mentor->user_id,
        ]);

        $created = User::where('username', 'new_client')->first();
        $this->assertTrue(Hash::check('Password123!', $created->password));
        $this->assertTrue($created->options()->whereKey($option->option_id)->exists());
        $this->assertTrue($created->roles()->where('role', Role::CLIENT)->exists());
    }

    /** @test */
    public function test_mentor_can_edit_existing_client(): void
    {
        $client = User::factory()->create([
            'mentor_id' => $this->mentor->user_id,
            'organisation_id' => $this->mentor->organisation_id,
            'language_id' => $this->mentor->language_id,
            'active' => true,
        ]);
        $client->roles()->attach($this->clientRole->role_id);

        Livewire::actingAs($this->mentor)
            ->test(ClientsManager::class)
            ->call('startEdit', $client->user_id)
            ->set('form.first_name', 'Updated')
            ->set('form.username', 'updated_username')
            ->call('save')
            ->assertDispatched('crud-record-saved');

        $this->assertDatabaseHas('users', [
            'user_id' => $client->user_id,
            'first_name' => 'Updated',
            'username' => 'updated_username',
        ]);
    }

    /** @test */
    public function test_mentor_can_toggle_client_active_state(): void
    {
        $client = User::factory()->create([
            'mentor_id' => $this->mentor->user_id,
            'organisation_id' => $this->mentor->organisation_id,
            'language_id' => $this->mentor->language_id,
            'active' => true,
        ]);
        $client->roles()->attach($this->clientRole->role_id);

        Livewire::actingAs($this->mentor)
            ->test(ClientsManager::class)
            ->call('requestToggle', $client->user_id)
            ->set('toggleModalWillActivate', false)
            ->call('confirmToggle');

        $this->assertDatabaseHas('users', [
            'user_id' => $client->user_id,
            'active' => false,
        ]);
    }

    /** @test */
    public function test_non_mentor_users_cannot_access_component(): void
    {
        $client = User::factory()->create([
            'first_name' => 'Client',
            'username' => 'client_user',
        ]);
        $client->roles()->attach($this->clientRole->role_id);

        Livewire::actingAs($client)
            ->test(ClientsManager::class)
            ->assertForbidden();
    }
}

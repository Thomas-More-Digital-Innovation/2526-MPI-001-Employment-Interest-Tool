<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Language;
use App\Models\Role;
use App\Livewire\Mentor\ClientCreateModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\ManagesTestData;

class ClientCreateModalUnitTest extends TestCase
{
    use RefreshDatabase, ManagesTestData;

    public function test_component_renders_modal()
    {
        // Arrange: Create a mentor user with required data
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientCreateModal::class);

        // Assert: Component renders successfully
        $component->assertOk();
    }

    public function test_modal_opens_when_open_modal_is_called()
    {
        // Arrange: Create a mentor user with required data
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Open modal
        $component = Livewire::test(ClientCreateModal::class)
            ->call('openModal');

        // Assert: Modal is shown
        $component->assertSet('showModal', true);
    }

    public function test_modal_closes_when_close_modal_is_called()
    {
        // Arrange: Create a mentor user with required data
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Open and close modal
        $component = Livewire::test(ClientCreateModal::class)
            ->call('openModal')
            ->call('closeModal');

        // Assert: Modal is closed
        $component->assertSet('showModal', false);
    }

    public function test_create_client_validates_required_fields()
    {
        // Arrange: Create a mentor user with required data
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Try to create client without required fields
        $component = Livewire::test(ClientCreateModal::class)
            ->call('openModal')
            ->call('createClient');

        // Assert: Validation errors are shown for required fields
        $component->assertHasErrors([
            'first_name',
            'last_name',
            'username',
            'password',
        ]);
        
        // Check for specific validation rules
        $component->assertHasErrors([
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);
        
        // vision_type has a default value, so it shouldn't trigger a required error
        $component->assertHasNoErrors('vision_type');
    }

    public function test_create_client_successfully_creates_client()
    {
        // Arrange: Create test data
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        $organisation = $testData['organisation'];
        $language = $testData['language'];
        
        $this->actingAs($mentor);

        // Act: Create client with valid data
        $component = Livewire::test(ClientCreateModal::class)
            ->call('openModal')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('username', 'johndoe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('organisation_id', $organisation->organisation_id)
            ->set('language_id', $language->language_id)
            ->set('vision_type', 'normal')
            ->call('createClient');

        // Assert: Client is created and modal is closed
        $component->assertSet('showModal', false)
                 ->assertSet('first_name', '')
                 ->assertSet('last_name', '');

        // Verify client exists in database
        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'vision_type' => 'normal',
            'mentor_id' => $mentor->user_id,
        ]);
    }

    public function test_create_client_assigns_client_role()
    {
        // Arrange: Create test data
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        $organisation = $testData['organisation'];
        $language = $testData['language'];
        
        $this->actingAs($mentor);

        // Act: Create client with valid data
        Livewire::test(ClientCreateModal::class)
            ->call('openModal')
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('username', 'janesmith')
            ->set('email', 'jane@example.com')
            ->set('password', 'password123')
            ->set('organisation_id', $organisation->organisation_id)
            ->set('language_id', $language->language_id)
            ->set('vision_type', 'normal')
            ->call('createClient');

        // Assert: Client role is assigned
        $client = User::where('username', 'janesmith')->first();
        $this->assertTrue($client->hasRole(Role::CLIENT));
    }

    public function test_username_must_be_unique()
    {
        // Arrange: Create test data and a client with existing username
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        
        // Create a client with the username we'll try to duplicate
        $this->createClientUser($mentor, ['username' => 'existinguser']);
        
        $this->actingAs($mentor);
        
        // Act & Assert: Try to create a new client with the same username
        Livewire::test(ClientCreateModal::class)
            ->call('openModal')
            ->set('username', 'existinguser')
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('password', 'password123')
            ->set('organisation_id', $mentor->organisation_id)
            ->set('language_id', $mentor->language_id)
            ->set('vision_type', 'normal')
            ->call('createClient')
            ->assertHasErrors(['username']);
    }
    public function test_password_must_be_at_least_8_characters()
    {
        // Arrange: Create test data
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Try to create client with short password
        $component = Livewire::test(ClientCreateModal::class)
            ->call('openModal')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('username', 'johndoe')
            ->set('email', 'john@example.com')
            ->set('password', 'short') // Too short
            ->set('organisation_id', $mentor->organisation_id)
            ->set('language_id', $mentor->language_id)
            ->set('vision_type', 'normal')
            ->call('createClient');

        // Assert: Password validation fails with min:8 error
        $component->assertHasErrors(['password' => 'min']);
    }
}

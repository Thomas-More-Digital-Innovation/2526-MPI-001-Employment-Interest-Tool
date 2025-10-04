<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Language;
use App\Models\Role;
use App\Livewire\Mentor\ClientEditModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\ManagesTestData;

class ClientEditModalUnitTest extends TestCase
{
    use RefreshDatabase, ManagesTestData;

    public function test_component_renders_modal()
    {
        // Arrange: Create a mentor user with required data
        $mentor = $this->createMentorUser();
        $this->actingAs($mentor);

        // Act: Render component
        $component = Livewire::test(ClientEditModal::class);

        // Assert: Component renders successfully
        $component->assertOk();
    }

    public function test_open_modal_populates_form_with_client_data()
    {
        // Arrange: Create test data
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        $organisation = $testData['organisation'];
        $language = $testData['language'];

        $client = $this->createClientUser($mentor, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'vision_type' => 'colorblind',
            'is_sound_on' => false,
            'active' => true,
        ]);

        $this->actingAs($mentor);

        // Act: Open modal for client
        $component = Livewire::test(ClientEditModal::class)
            ->call('openModal', $client->user_id);

        // Assert: Form is populated with client data
        $component->assertSet('clientId', $client->user_id)
                 ->assertSet('first_name', 'John')
                 ->assertSet('last_name', 'Doe')
                 ->assertSet('username', 'johndoe')
                 ->assertSet('email', 'john@example.com')
                 ->assertSet('organisation_id', $organisation->organisation_id)
                 ->assertSet('language_id', $language->language_id)
                 ->assertSet('vision_type', 'colorblind')
                 ->assertSet('is_sound_on', false)
                 ->assertSet('active', true)
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
        $component = Livewire::test(ClientEditModal::class)
            ->call('openModal', $client->user_id);

        // Assert: Modal doesn't open and error is dispatched
        $component->assertSet('showModal', false)
                 ->assertDispatched('error');
    }

    public function test_update_client_successfully_updates_client_data()
    {
        // Arrange: Create test data
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        $organisation = $testData['organisation'];
        
        // Create a second organisation
        $newOrganisation = Organisation::create([
            'name' => 'New Organisation',
            'active' => true,
            'address' => '456 New St',
            'postal_code' => '2000',
            'city' => 'New City',
            'country' => 'New Country',
            'email' => 'new@example.com',
            'phone' => '9876543210',
        ]);

        $client = $this->createClientUser($mentor, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'organisation_id' => $organisation->organisation_id,
        ]);

        $this->actingAs($mentor);

        // Act: Update client data
        $component = Livewire::test(ClientEditModal::class)
            ->call('openModal', $client->user_id)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('organisation_id', $newOrganisation->organisation_id)
            ->call('updateClient');

        // Assert: Client is updated and modal is closed
        $component->assertSet('showModal', false);

        // Verify database changes
        $client->refresh();
        $this->assertEquals('Jane', $client->first_name);
        $this->assertEquals('Smith', $client->last_name);
        $this->assertEquals($newOrganisation->organisation_id, $client->organisation_id);
    }

    public function test_update_client_validates_required_fields()
    {
        // Arrange: Create test data
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        
        // Create a client with all required fields
        $client = $this->createClientUser($mentor, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'organisation_id' => $testData['organisation']->organisation_id,
            'language_id' => $testData['language']->language_id,
            'vision_type' => 'normal',
            'is_sound_on' => true,
            'active' => true,
        ]);

        $this->actingAs($mentor);

        // Act: Open modal and clear required fields one by one
        $component = Livewire::test(ClientEditModal::class)
            ->call('openModal', $client->user_id);
            
        // Clear required fields
        $component->set('first_name', '')
                 ->set('last_name', '')
                 ->set('username', '')
                 ->set('organisation_id', '')
                 ->set('language_id', '')
                 ->set('vision_type', '');

        // Submit the form
        $component->call('updateClient');

        // Assert: Validation errors are shown for all required fields
        $component->assertHasErrors([
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'organisation_id' => 'required',
            'language_id' => 'required',
            'vision_type' => 'required',
        ]);
    }

    public function test_username_must_be_unique_when_updating()
    {
        // Arrange: Create test data
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        $organisation = $testData['organisation'];
        $language = $testData['language'];
        
        // Create two clients with different usernames
        $client1 = $this->createClientUser($mentor, [
            'username' => 'client1',
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);
        
        $client2 = $this->createClientUser($mentor, [
            'username' => 'client2',
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);

        $this->actingAs($mentor);

        // Act: Try to update client1's username to client2's username
        $component = Livewire::test(ClientEditModal::class)
            ->call('openModal', $client1->user_id)
            ->set('username', 'client2') // Same as client2
            ->set('organisation_id', $organisation->organisation_id)
            ->set('language_id', $language->language_id)
            ->call('updateClient');

        // Assert: Validation error for unique username
        $component->assertHasErrors(['username']);
    }

    public function test_password_is_optional_when_updating()
    {
        // Arrange: Create test data
        $testData = $this->setupTestData();
        $mentor = $testData['mentor'];
        
        $client = $this->createClientUser($mentor, [
            'username' => 'testuser',
            'organisation_id' => $testData['organisation']->organisation_id,
            'language_id' => $testData['language']->language_id,
        ]);

        $this->actingAs($mentor);

        // Act: Update client without changing password
        $component = Livewire::test(ClientEditModal::class)
            ->call('openModal', $client->user_id)
            ->set('first_name', 'Updated')
            ->call('updateClient');

        // Assert: Update succeeds without password
        $component->assertSet('showModal', false);
        $component->assertDispatched('client-updated');
    }

    public function test_close_modal_resets_form()
    {
        // Arrange: Create mentor and client
        $mentor = User::factory()->create();
        $client = User::factory()->create(['mentor_id' => $mentor->user_id]);

        $this->actingAs($mentor);

        // Act: Open modal, set some data, then close
        $component = Livewire::test(ClientEditModal::class)
            ->call('openModal', $client->user_id)
            ->set('first_name', 'Modified')
            ->call('closeModal');

        // Assert: Form is reset and modal is closed
        $component->assertSet('showModal', false)
                 ->assertSet('clientId', null)
                 ->assertSet('first_name', '');
    }
}

<?php

namespace Tests\Traits;

use App\Models\Organisation;
use App\Models\Language;
use App\Models\Role;
use App\Models\User;

/**
 * Trait for managing test data in client management tests
 */
trait ManagesTestData
{
    protected function createTestOrganisation(): Organisation
    {
        return Organisation::firstOrCreate(
            ['name' => 'Test Organisation'],
            [
                'active' => true,
                'address' => '123 Test St',
                'postal_code' => '1000',
                'city' => 'Test City',
                'country' => 'Test Country',
                'email' => 'test@example.com',
                'phone' => '1234567890',
            ]
        );
    }

    protected function createTestLanguage(): Language
    {
        return Language::firstOrCreate(
            ['language_code' => 'en'],
            ['language_name' => 'English']
        );
    }

    protected function createMentorUser(): User
    {
        $organisation = $this->createTestOrganisation();
        $language = $this->createTestLanguage();
        
        $mentor = User::factory()->create([
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);
        
        $mentorRole = Role::firstOrCreate(
            ['role' => 'Mentor'],
            ['receive_emails' => true]
        );
        
        $mentor->roles()->sync([$mentorRole->role_id]);
        
        return $mentor;
    }
    
    protected function createClientUser(User $mentor, array $attributes = []): User
    {
        $client = User::factory()->create(array_merge([
            'mentor_id' => $mentor->user_id,
            'organisation_id' => $mentor->organisation_id,
            'language_id' => $mentor->language_id,
            'active' => true,
        ], $attributes));
        
        $clientRole = Role::firstOrCreate(
            ['role' => 'Client'],
            ['receive_emails' => false]
        );
        
        $client->roles()->sync([$clientRole->role_id]);
        
        return $client;
    }
    
    protected function setupTestData(): array
    {
        $organisation = $this->createTestOrganisation();
        $language = $this->createTestLanguage();
        $mentor = $this->createMentorUser();
        
        return [
            'organisation' => $organisation,
            'language' => $language,
            'mentor' => $mentor,
        ];
    }
}

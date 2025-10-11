<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Faq;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $organisation = \App\Models\Organisation::create([
            'name' => 'Test Organisation',
            'active' => true,
        ]);

        $language = \App\Models\Language::create([
            'language_code' => 'nl',
            'language_name' => 'Dutch',
        ]);

        \App\Models\Language::create([
            'language_code' => 'en',
            'language_name' => 'English',
        ]);

        // Create roles
        $superAdminRole = Role::create([
            'role' => Role::SUPER_ADMIN,
            'receive_emails' => true,
        ]);

        $adminRole = Role::create([
            'role' => Role::ADMIN,
            'receive_emails' => true,
        ]);

        $mentorRole = Role::create([
            'role' => Role::MENTOR,
            'receive_emails' => false,
        ]);

        $clientRole = Role::create([
            'role' => Role::CLIENT,
            'receive_emails' => false,
        ]);

        $researcherRole = Role::create([
            'role' => Role::RESEARCHER,
            'receive_emails' => false,
        ]);

        // Create test users with different roles
        $superAdmin = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'vision_type' => 'normal',
            'is_sound_on' => false,
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'profile_picture_url' => 'default.png',
        ]);
        $superAdmin->roles()->attach($superAdminRole);

        $admin = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'vision_type' => 'normal',
            'is_sound_on' => false,
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'profile_picture_url' => 'default.png',
        ]);
        $admin->roles()->attach($adminRole);

        $mentor = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Mentor',
            'username' => 'mentor',
            'email' => 'mentor@example.com',
            'vision_type' => 'normal',
            'is_sound_on' => false,
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'profile_picture_url' => 'default.png',
        ]);
        $mentor->roles()->attach($mentorRole);

        $researcher = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Researcher',
            'username' => 'researcher',
            'email' => 'researcher@example.com',
            'vision_type' => 'normal',
            'is_sound_on' => false,
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'profile_picture_url' => 'default.png',
        ]);
        $researcher->roles()->attach($researcherRole);

        // Create a specific test user (client)
        $client = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'username' => 'client',
            'email' => 'client@example.com',
            'vision_type' => 'normal',
            'is_sound_on' => false,
            'password' => bcrypt('password'),
            'mentor_id' => $mentor->user_id,
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);
        $client->roles()->attach($clientRole);

       Faq::factory(5)->create();

       // Run the TestSeeder to populate test and question data
       $this->call(TestSeeder::class);
    }
}

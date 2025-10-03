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
            'language_code' => 'en',
            'language_name' => 'English',
        ]);

        \App\Models\Language::create([
            'language_code' => 'nl',
            'language_name' => 'Dutch',
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

        // Create test users with different roles
        $superAdmin = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);
        $superAdmin->roles()->attach($superAdminRole);

        $admin = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);
        $admin->roles()->attach($adminRole);

        $mentor = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Mentor',
            'username' => 'mentor',
            'email' => 'mentor@example.com',
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);
        $mentor->roles()->attach($mentorRole);

        // Create a specific test user (client)
        $client = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'username' => 'client',
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'mentor_id' => $mentor->user_id,
        ]);
        $client->roles()->attach($clientRole);

       Faq::factory(5)->create();

       // Run the TestSeeder to populate test and question data
       $this->call(TestSeeder::class);
    }
}

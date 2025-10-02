<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Faq;
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
       
        // Create a specific test user
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
        ]);
      
       Faq::factory(5)->create();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Seed the test database table with one test and three questions.
     */
    public function run(): void
    {
        // Create interest fields first
        $interestFields = [
            [
                'name' => 'Technology',
                'description' => 'Interest in technology and digital solutions',
            ],
            [
                'name' => 'Healthcare',
                'description' => 'Interest in healthcare and medical field',
            ],
            [
                'name' => 'Education',
                'description' => 'Interest in education and teaching',
            ],
        ];

        DB::table('interest_field')->insert($interestFields);

        // Get the inserted interest field IDs
        $techFieldId = DB::table('interest_field')->where('name', 'Technology')->first()->interest_field_id;
        $healthFieldId = DB::table('interest_field')->where('name', 'Healthcare')->first()->interest_field_id;
        $eduFieldId = DB::table('interest_field')->where('name', 'Education')->first()->interest_field_id;

        // Create a test
        $testId = DB::table('test')->insertGetId([
            'test_name' => 'Employment Interest Test',
            'active' => true,
        ]);

        // Create three questions with different interest fields
        $questions = [
            [
                'question' => 'How comfortable are you with using computer software and digital tools?',
                'test_id' => $testId,
                'interest_field_id' => $techFieldId,
                'question_number' => 1,
                'image_description' => 'Computer with software interface',
            ],
            [
                'question' => 'Would you be interested in helping people with their health and wellbeing?',
                'test_id' => $testId,
                'interest_field_id' => $healthFieldId,
                'question_number' => 2,
                'image_description' => 'Healthcare professional helping a patient',
            ],
            [
                'question' => 'Do you enjoy explaining concepts and helping others learn new skills?',
                'test_id' => $testId,
                'interest_field_id' => $eduFieldId,
                'question_number' => 3,
                'image_description' => 'Teacher presenting to students',
            ],
        ];

        DB::table('question')->insert($questions);

        // Attach the test to the client user
        $clientUser = DB::table('users')->where('username', 'client')->first();
        
        if ($clientUser) {
            DB::table('user_test')->insert([
                'user_id' => $clientUser->user_id,
                'test_id' => $testId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

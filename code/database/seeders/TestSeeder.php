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
                'name' => 'Technologie',
                'description' => 'Interesse in technologie en digitale oplossingen',
            ],
            [
                'name' => 'Gezondheidszorg',
                'description' => 'Interesse in gezondheidszorg en medisch gebied',
            ],
            [
                'name' => 'Onderwijs',
                'description' => 'Interesse in onderwijs en lesgeven',
            ],
        ];

        DB::table('interest_field')->insert($interestFields);

        // Get the inserted interest field IDs
        $techFieldId = DB::table('interest_field')->where('name', 'Technologie')->first()->interest_field_id;
        $healthFieldId = DB::table('interest_field')->where('name', 'Gezondheidszorg')->first()->interest_field_id;
        $eduFieldId = DB::table('interest_field')->where('name', 'Onderwijs')->first()->interest_field_id;

        // Create a test
        $testId = DB::table('test')->insertGetId([
            'test_name' => 'Arbeidsinteresse Test',
            'active' => true,
        ]);

        // Create three questions with different interest fields
        $questions = [
            [
                'question' => 'Hoe comfortabel voel je je bij het gebruik van computersoftware en digitale hulpmiddelen?',
                'test_id' => $testId,
                'interest_field_id' => $techFieldId,
                'question_number' => 1,
                'image_description' => 'Computer met software-interface',
            ],
            [
                'question' => 'Zou je geïnteresseerd zijn in het helpen van mensen met hun gezondheid en welzijn?',
                'test_id' => $testId,
                'interest_field_id' => $healthFieldId,
                'question_number' => 2,
                'image_description' => 'Zorgprofessional die een patiënt helpt',
            ],
            [
                'question' => 'Vind je het leuk om concepten uit te leggen en anderen te helpen nieuwe vaardigheden te leren?',
                'test_id' => $testId,
                'interest_field_id' => $eduFieldId,
                'question_number' => 3,
                'image_description' => 'Leraar die presenteert aan studenten',
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

            // Create a test attempt for the client user
            $testAttemptId = DB::table('test_attempt')->insertGetId([
                'test_id' => $testId,
                'user_id' => $clientUser->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get all questions for this test
            $testQuestions = DB::table('question')->where('test_id', $testId)->get();

            // Create answers for each question
            foreach ($testQuestions as $question) {
                DB::table('answer')->insert([
                    'answer' => rand(0, 1), // Random true/false answer
                    'response_time' => rand(1000, 15000), // Random response time between 1-15 seconds (in milliseconds)
                    'unclear' => 0,
                    'question_id' => $question->question_id,
                    'test_attempt_id' => $testAttemptId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\InterestField;
use App\Models\InterestFieldTranslation;
use App\Models\Question;
use App\Models\QuestionTranslation;

class TestSeeder extends Seeder
{
    /**
     * Seed the test database table with one test and three questions.
     */
    public function run(): void
    {
        // Get language IDs
        $dutchLanguageId = DB::table('language')->where('language_code', 'nl')->first()->language_id;
        $englishLanguageId = DB::table('language')->where('language_code', 'en')->first()->language_id;

        // Create interest fields (in Dutch)
        $techField = InterestField::create([
            'name' => 'Technologie',
            'description' => 'Interesse in technologie en digitale oplossingen',
        ]);

        $healthField = InterestField::create([
            'name' => 'Gezondheidszorg',
            'description' => 'Interesse in gezondheidszorg en medisch gebied',
        ]);

        $eduField = InterestField::create([
            'name' => 'Onderwijs',
            'description' => 'Interesse in onderwijs en lesgeven',
        ]);

        $creativeField = InterestField::create([
            'name' => 'Creatieve Kunsten',
            'description' => 'Interesse in creatieve en artistieke activiteiten',
        ]);

        $businessField = InterestField::create([
            'name' => 'Zakenleven',
            'description' => 'Interesse in zakenleven en ondernemerschap',
        ]);

        $scienceField = InterestField::create([
            'name' => 'Wetenschap',
            'description' => 'Interesse in wetenschappelijk onderzoek en ontdekking',
        ]);

        // Get the interest field IDs from the created models
        $techFieldId = $techField->interest_field_id;
        $healthFieldId = $healthField->interest_field_id;
        $eduFieldId = $eduField->interest_field_id;
        $creativeFieldId = $creativeField->interest_field_id;
        $businessFieldId = $businessField->interest_field_id;
        $scienceFieldId = $scienceField->interest_field_id;

        // Interest field translations (English)
        InterestFieldTranslation::create([
            'interest_field_id' => $techFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Technology',
            'description' => 'Interest in technology and digital solutions',
        ]);

        InterestFieldTranslation::create([
            'interest_field_id' => $healthFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Healthcare',
            'description' => 'Interest in healthcare and medical field',
        ]);

        InterestFieldTranslation::create([
            'interest_field_id' => $eduFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Education',
            'description' => 'Interest in education and teaching',
        ]);

        InterestFieldTranslation::create([
            'interest_field_id' => $creativeFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Creative Arts',
            'description' => 'Interest in creative and artistic activities',
        ]);

        InterestFieldTranslation::create([
            'interest_field_id' => $businessFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Business',
            'description' => 'Interest in business and entrepreneurship',
        ]);

        InterestFieldTranslation::create([
            'interest_field_id' => $scienceFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Science',
            'description' => 'Interest in scientific research and discovery',
        ]);

        // Create test 1
        $testOne = DB::table('test')->insertGetId([
            'test_name' => 'Arbeidsinteresse Test',
            'active' => true,
        ]);

        // Create questions for test 1
        $question1 = Question::create([
            'question' => 'Hoe comfortabel voel je je bij het gebruik van computersoftware en digitale hulpmiddelen?',
            'test_id' => $testOne,
            'interest_field_id' => $techFieldId,
            'question_number' => 1,
            'image_description' => 'Computer met software-interface',
        ]);

        $question2 = Question::create([
            'question' => 'Zou je geïnteresseerd zijn in het helpen van mensen met hun gezondheid en welzijn?',
            'test_id' => $testOne,
            'interest_field_id' => $healthFieldId,
            'question_number' => 2,
            'image_description' => 'Zorgprofessional die een patiënt helpt',
        ]);

        $question3 = Question::create([
            'question' => 'Vind je het leuk om concepten uit te leggen en anderen te helpen nieuwe vaardigheden te leren?',
            'test_id' => $testOne,
            'interest_field_id' => $eduFieldId,
            'question_number' => 3,
            'image_description' => 'Leraar die presenteert aan studenten',
        ]);

        $question1Id = $question1->question_id;
        $question2Id = $question2->question_id;
        $question3Id = $question3->question_id;

        // Question translations for test 1 (English)
        QuestionTranslation::create([
            'question_id' => $question1Id,
            'language_id' => $englishLanguageId,
            'question' => 'How comfortable do you feel using computer software and digital tools?',
            'image_description' => 'Computer with software interface',
        ]);

        QuestionTranslation::create([
            'question_id' => $question2Id,
            'language_id' => $englishLanguageId,
            'question' => 'Would you be interested in helping people with their health and well-being?',
            'image_description' => 'Healthcare professional helping a patient',
        ]);

        QuestionTranslation::create([
            'question_id' => $question3Id,
            'language_id' => $englishLanguageId,
            'question' => 'Do you enjoy explaining concepts and helping others learn new skills?',
            'image_description' => 'Teacher presenting to students',
        ]);

        // Get the client user
        $clientUser = DB::table('users')->where('username', 'client')->first();

        if ($clientUser) {
            DB::table('user_test')->insert([
                'user_id' => $clientUser->user_id,
                'test_id' => $testOne,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create a test attempt for test 1
            $testAttemptId = DB::table('test_attempt')->insertGetId([
                'test_id' => $testOne,
                'user_id' => $clientUser->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get all questions for test 1
            $testQuestions = DB::table('question')->where('test_id', $testOne)->get();

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

        // Create test 2
        $testTwo = DB::table('test')->insertGetId([
            'test_name' => '1-2-3',
            'active' => true,
        ]);

        // Create questions for test 2
        $testTwoQuestion1 = Question::create([
            'question' => 'Testvraag één',
            'test_id' => $testTwo,
            'interest_field_id' => $creativeFieldId,
            'question_number' => 1,
            'image_description' => 'Eerste test afbeelding',
        ]);

        $testTwoQuestion2 = Question::create([
            'question' => 'Testvraag twee',
            'test_id' => $testTwo,
            'interest_field_id' => $businessFieldId,
            'question_number' => 2,
            'image_description' => 'Tweede test afbeelding',
        ]);

        $testTwoQuestion3 = Question::create([
            'question' => 'Testvraag drie',
            'test_id' => $testTwo,
            'interest_field_id' => $scienceFieldId,
            'question_number' => 3,
            'image_description' => 'Derde test afbeelding',
        ]);

        $testTwoQuestion1Id = $testTwoQuestion1->question_id;
        $testTwoQuestion2Id = $testTwoQuestion2->question_id;
        $testTwoQuestion3Id = $testTwoQuestion3->question_id;

        // Dutch translations for test 2
        QuestionTranslation::create([
            'question_id' => $testTwoQuestion1Id,
            'language_id' => $dutchLanguageId,
            'question' => 'Testvraag één',
            'image_description' => 'Eerste test afbeelding',
        ]);

        QuestionTranslation::create([
            'question_id' => $testTwoQuestion2Id,
            'language_id' => $dutchLanguageId,
            'question' => 'Testvraag twee',
            'image_description' => 'Tweede test afbeelding',
        ]);

        QuestionTranslation::create([
            'question_id' => $testTwoQuestion3Id,
            'language_id' => $dutchLanguageId,
            'question' => 'Testvraag drie',
            'image_description' => 'Derde test afbeelding',
        ]);

        // Attach the test 2 to the client user
        if ($clientUser) {
            DB::table('user_test')->insert([
                'user_id' => $clientUser->user_id,
                'test_id' => $testTwo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create a test attempt for test 2
            $testTwoAttemptId = DB::table('test_attempt')->insertGetId([
                'test_id' => $testTwo,
                'user_id' => $clientUser->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get all questions for test 2
            $testTwoQuestions = DB::table('question')->where('test_id', $testTwo)->get();

            // Create answers for each test 2 question
            foreach ($testTwoQuestions as $question) {
                DB::table('answer')->insert([
                    'answer' => rand(0, 1), // Random true/false answer
                    'response_time' => rand(1000, 15000), // Random response time between 1-15 seconds (in milliseconds)
                    'unclear' => 0,
                    'question_id' => $question->question_id,
                    'test_attempt_id' => $testTwoAttemptId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

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

        $computerField = InterestField::create([
            'name' => 'Computer/IT',
            'description' => 'Interesse in computers, netwerken en programmeren',
        ]);

        $accountingField = InterestField::create([
            'name' => 'Boekhouding',
            'description' => 'Interesse in financiële administratie en boekhoudkundige taken',
        ]);

        $retailField = InterestField::create([
            'name' => 'Winkel/Detailhandel',
            'description' => 'Interesse in klantenservice en winkelactiviteiten',
        ]);

        // Get the interest field IDs from the created models
        $techFieldId = $techField->interest_field_id;
        $healthFieldId = $healthField->interest_field_id;
        $eduFieldId = $eduField->interest_field_id;
        $computerFieldId = $computerField->interest_field_id;
        $accountingFieldId = $accountingField->interest_field_id;
        $retailFieldId = $retailField->interest_field_id;

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
            'interest_field_id' => $computerFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Computer/IT',
            'description' => 'Interest in computers, networks and programming',
        ]);

        InterestFieldTranslation::create([
            'interest_field_id' => $accountingFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Accounting',
            'description' => 'Interest in financial administration and bookkeeping tasks',
        ]);

        InterestFieldTranslation::create([
            'interest_field_id' => $retailFieldId,
            'language_id' => $englishLanguageId,
            'name' => 'Retail/Sales',
            'description' => 'Interest in customer service and retail activities',
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
            'test_name' => 'Demo Test',
            'active' => true,
        ]);

        // Create questions for test 2
        // Computer/IT questions
        $testTwoQuestion1 = Question::create([
            'question' => 'Zou je geïnteresseerd zijn in het configureren en onderhouden van netwerkapparatuur zoals routers?',
            'test_id' => $testTwo,
            'interest_field_id' => $computerFieldId,
            'question_number' => 1,
            'media_link' => 'network-tech.png',
            'image_description' => 'Netwerktechnicus die routers configureert',
        ]);

        $testTwoQuestion2 = Question::create([
            'question' => 'Vind je het leuk om computerprogramma\'s te schrijven en code te ontwikkelen?',
            'test_id' => $testTwo,
            'interest_field_id' => $computerFieldId,
            'question_number' => 2,
            'media_link' => 'programming.png',
            'image_description' => 'Programmeur die code schrijft op computer',
        ]);

        // Accounting questions
        $testTwoQuestion3 = Question::create([
            'question' => 'Zou je het prettig vinden om administratieve taken op papier uit te voeren en documenten te organiseren?',
            'test_id' => $testTwo,
            'interest_field_id' => $accountingFieldId,
            'question_number' => 3,
            'media_link' => 'accounting.png',
            'image_description' => 'Boekhouder die documenten op papier verwerkt',
        ]);

        $testTwoQuestion4 = Question::create([
            'question' => 'Ben je geïnteresseerd in het tellen en beheren van geld en financiële transacties?',
            'test_id' => $testTwo,
            'interest_field_id' => $accountingFieldId,
            'question_number' => 4,
            'media_link' => 'money-counting.png',
            'image_description' => 'Persoon die geld telt en financiën beheert',
        ]);

        // Retail questions
        $testTwoQuestion5 = Question::create([
            'question' => 'Zou je het leuk vinden om als kassamedewerker klanten te helpen bij het afrekenen?',
            'test_id' => $testTwo,
            'interest_field_id' => $retailFieldId,
            'question_number' => 5,
            'media_link' => 'cashier.png',
            'image_description' => 'Kassamedewerker die klant helpt bij kassa',
        ]);

        $testTwoQuestion6 = Question::create([
            'question' => 'Ben je geïnteresseerd in het beheren van voorraad en het organiseren van winkelproducten?',
            'test_id' => $testTwo,
            'interest_field_id' => $retailFieldId,
            'question_number' => 6,
            'media_link' => 'inventory-management.png',
            'image_description' => 'Medewerker die voorraad organiseert in winkel',
        ]);

        $testTwoQuestion1Id = $testTwoQuestion1->question_id;
        $testTwoQuestion2Id = $testTwoQuestion2->question_id;
        $testTwoQuestion3Id = $testTwoQuestion3->question_id;
        $testTwoQuestion4Id = $testTwoQuestion4->question_id;
        $testTwoQuestion5Id = $testTwoQuestion5->question_id;
        $testTwoQuestion6Id = $testTwoQuestion6->question_id;

        // Create translations for test 2 questions (English)
        QuestionTranslation::create([
            'question_id' => $testTwoQuestion1Id,
            'language_id' => $englishLanguageId,
            'question' => 'Would you be interested in configuring and maintaining network equipment such as routers?',
            'image_description' => 'Network technician configuring routers',
        ]);

        QuestionTranslation::create([
            'question_id' => $testTwoQuestion2Id,
            'language_id' => $englishLanguageId,
            'question' => 'Do you enjoy writing computer programs and developing code?',
            'media_link' => 'programming.png',
            'image_description' => 'Programmer writing code on computer',
        ]);

        QuestionTranslation::create([
            'question_id' => $testTwoQuestion3Id,
            'language_id' => $englishLanguageId,
            'question' => 'Would you find it pleasant to perform administrative tasks on paper and organize documents?',
            'media_link' => 'accounting.png',
            'image_description' => 'Accountant processing paper documents',
        ]);

        QuestionTranslation::create([
            'question_id' => $testTwoQuestion4Id,
            'language_id' => $englishLanguageId,
            'question' => 'Are you interested in counting and managing money and financial transactions?',
            'media_link' => 'money-counting.png',
            'image_description' => 'Person counting money and managing finances',
        ]);

        QuestionTranslation::create([
            'question_id' => $testTwoQuestion5Id,
            'language_id' => $englishLanguageId,
            'question' => 'Would you enjoy working as a cashier helping customers with checkout?',
            'media_link' => 'cashier.png',
            'image_description' => 'Cashier helping customer at checkout',
        ]);

        QuestionTranslation::create([
            'question_id' => $testTwoQuestion6Id,
            'language_id' => $englishLanguageId,
            'question' => 'Are you interested in managing inventory and organizing store products?',
            'media_link' => 'inventory-management.png',
            'image_description' => 'Employee organizing inventory in store',
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

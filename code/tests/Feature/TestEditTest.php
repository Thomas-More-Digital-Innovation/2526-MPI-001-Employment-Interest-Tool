<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Roles\Superadmin\TestEdit;
use App\Models\Test;
use App\Models\Question;
use App\Models\InterestField;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    Route::get('/superadmin/test/create', fn () => 'ok')->name('superadmin.test.create');
});

test('mount loads tests list', function () {
    Test::factory()->count(3)->create();

    Livewire::test(TestEdit::class)
        ->assertSet('tests', fn ($val) => count($val) === 3);
});

test('loadTest redirects and seeds session for TestCreation', function () {
    // create the interest field to satisfy FK
    $if = InterestField::factory()->create();

    $test = Test::create(['test_name' => 'Demo']);

    Question::create([
        'test_id'            => $test->test_id,
        'interest_field_id'  => $if->interest_field_id, // use real FK
        'question_number'    => 1,
        'question'           => 'Q1',
        'image_description'  => 'desc',
        'media_link'         => 'img.png',
        'sound_link'         => 's.webm',
    ]);

    Livewire::test(TestEdit::class)
        ->call('loadTest', $test->test_id)
        ->assertRedirect(route('superadmin.test.create'));

    expect(session('edit_test_id'))->toBe($test->test_id);
    expect(session('edit_test_name'))->toBe('Demo');
    $payload = session('edit_questions');
    expect($payload)->toBeArray()
        ->and($payload[0]['title'])->toBe('Q1')
        ->and($payload[0]['circleFill'])->toBe('green');
});

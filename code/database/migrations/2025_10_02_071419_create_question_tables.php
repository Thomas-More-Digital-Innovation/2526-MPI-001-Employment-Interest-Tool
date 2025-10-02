<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tables: test, interest_field, question
     */
    public function up(): void
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id('test_id');
            $table->string('test_name');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('interest_field', function (Blueprint $table) {
            $table->id('interest_field_id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('question', function (Blueprint $table) {
            $table->id('question_id');
            $table->string('question');
            $table->string('media_link')->nullable();
            $table->string('sound_link')->nullable();
            $table->foreignId('test_id') // Foreign key to test table
                ->constrained('test', 'test_id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('interest_field_id') // Foreign key to interest field table
                ->constrained('interest_field', 'interest_field_id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('question_number');
            $table->string('image_description')->nullable();
            $table->timestamps();
        });



        // Schema::create('language', function (Blueprint $table) {
        //     $table->id('language_id');
        //     $table->string('language_code');
        //     $table->string('language_name');
        //     $table->timestamps();
        // });

        // Schema::create('question_translation', function (Blueprint $table) {
        //     $table->id('question_translation_id');
        //     $table->foreignId('question_id') // Foreign key to question table
        //         ->constrained('question', 'question_id')
        //         ->onDelete('cascade')
        //         ->onUpdate('cascade');
        //     $table->foreignId('language_id') // Foreign key to language table
        //         ->constrained('language', 'language_id')
        //         ->onDelete('cascade')
        //         ->onUpdate('cascade');
        //     $table->string('question');
        //     $table->string('media_link')->nullable();
        //     $table->string('sound_link')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('question_translation');
        // Schema::dropIfExists('language');
        Schema::dropIfExists('question');
        Schema::dropIfExists('interest_field');
        Schema::dropIfExists('test');
    }
};

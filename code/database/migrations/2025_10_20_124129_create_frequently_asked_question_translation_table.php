<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('frequently_asked_question_translation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('frequently_asked_question_id');
            $table->unsignedBigInteger('language_id');
            $table->string('question');
            $table->text('answer')->nullable();
            $table->timestamps();

            $table->unique(['frequently_asked_question_id', 'language_id'], 'faq_lang_unique');

            $table->foreign('frequently_asked_question_id', 'fk_faq_translation_faq')
                ->references('id')->on('frequently_asked_question')
                ->onDelete('cascade');

            $table->foreign('language_id', 'fk_faq_translation_language')
                ->references('id')->on('languages')
                ->onDelete('cascade');
        });
    }

    
    /**
     * Reverse the migrations.
    */
    public function down()
    {
        Schema::table('frequently_asked_question_translation', function (Blueprint $table) {
            $table->dropForeign('fk_faq_translation_faq');
            $table->dropForeign('fk_faq_translation_language');
            $table->dropUnique('faq_lang_unique');
        });

        Schema::dropIfExists('frequently_asked_question_translation');
    }
};

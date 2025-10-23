<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrequentlyAskedQuestionTranslation extends Model
{
    protected $table = 'frequently_asked_question_translation';
    protected $primaryKey = 'frequently_asked_question_translation_id';

    protected $fillable = [
        'frequently_asked_question_id',
        'language_id',
        'question',
        'answer',
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class, 'frequently_asked_question_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

    use HasFactory;

    protected $table = 'question';
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'question',
        'media_link',
        'sound_link',
        'test_id',
        'interest_field_id',
        'question_number',
        'image_description'
    ];

    // Relationships
    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function interestField()
    {
        return $this->belongsTo(InterestField::class, 'interest_field_id', 'interest_field_id');
    }

    public function questionTranslations()
    {
//        return $this->hasMany(QuestionTranslation::class, 'questionId', 'questionId');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }
}

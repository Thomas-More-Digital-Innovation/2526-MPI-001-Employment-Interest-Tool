<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestField extends Model
{

    use HasFactory;

    protected $table = 'interest_field';
    protected $primaryKey = 'interest_field_id';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships
    public function questions()
    {
        return $this->hasMany(Question::class, 'interest_field_id', 'interest_field_id');
    }

    public function interestFieldTranslations()
    {
//        return $this->hasMany(IntrestFieldTranslation::class, 'intrestFieldId', 'intrestFieldId');
    }
}

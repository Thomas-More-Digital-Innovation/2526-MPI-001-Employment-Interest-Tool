<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{

    use HasFactory;

    protected $table = 'test';
    protected $primaryKey = 'test_id';

    protected $fillable = [
        'test_name',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Relationships
    public function organisationTests()
    {
//        return $this->hasMany(OrganisationTest::class, 'testId', 'testId');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'test_id', 'test_id');
    }
}



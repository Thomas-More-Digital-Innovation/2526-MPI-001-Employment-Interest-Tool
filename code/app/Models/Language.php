<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'language';
    use HasFactory;

    protected $primaryKey = 'language_id';
    protected $fillable = [
        'language_code',
        'language_name',
    ];
}

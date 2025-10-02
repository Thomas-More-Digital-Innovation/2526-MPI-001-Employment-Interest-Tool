<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Faq extends Model
{
    use HasFactory;
    protected $table = 'frequently_asked_question';

    protected $fillable = ['question', 'answer'];
}

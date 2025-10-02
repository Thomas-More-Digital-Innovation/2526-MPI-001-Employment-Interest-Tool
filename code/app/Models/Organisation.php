<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $table = 'organisation';
    use HasFactory;

    protected $primaryKey = 'organisation_id';
    protected $fillable = [
        'name',
        'active',
        'expire_date',
    ];
}

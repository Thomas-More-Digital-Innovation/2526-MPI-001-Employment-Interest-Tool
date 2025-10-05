<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'option_id';

    /**
     * The table associated with the model.
     */
    protected $table = 'option';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'option_name',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => 'string',
        ];
    }

    /**
     * Option type constants.
     */
    public const TYPE_DISABILITY = 'disability';

    /**
     * Users associated with the option.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_option', 'option_id', 'user_id');
    }
}

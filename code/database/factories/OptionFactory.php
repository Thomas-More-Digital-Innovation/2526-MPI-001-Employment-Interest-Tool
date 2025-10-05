<?php

namespace Database\Factories;

use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Option>
 */
class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition(): array
    {
        return [
            'option_name' => fake()->unique()->words(2, true),
            'type' => Option::TYPE_DISABILITY,
        ];
    }
}

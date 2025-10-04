<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'role' => $this->faker->unique()->randomElement(['SuperAdmin', 'Admin', 'Mentor', 'Client']),
            'receive_emails' => $this->faker->boolean,
        ];
    }
}

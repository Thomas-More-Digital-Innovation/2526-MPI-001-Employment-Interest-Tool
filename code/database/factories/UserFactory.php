<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            // Assign client role by default if no roles are set
            if ($user->roles->isEmpty()) {
                $clientRole = \App\Models\Role::firstOrCreate(
                    ['role' => \App\Models\Role::CLIENT],
                    ['receive_emails' => false]
                );
                $user->roles()->attach($clientRole);
            }
        });
    }

    public function definition(): array
    {
        // Create or get default organisation
        $organisation = Organisation::firstOrCreate(
            ['name' => 'Test Organisation'],
            [
                'active' => true,
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'country' => fake()->country(),
                'email' => fake()->companyEmail(),
                'phone' => fake()->phoneNumber(),
            ]
        );

        // Create or get default language
        $language = Language::firstOrCreate(
            ['language_code' => 'en'],
            ['language_name' => 'English']
        );

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'is_sound_on' => fake()->boolean(),
            'vision_type' => fake()->randomElement(['normal', 'colorblind', 'low-vision']),
            'mentor_id' => null,
            'organisation_id' => $organisation->organisation_id,
            'language_id' => $language->language_id,
            'first_login' => true,
            'active' => true,
            'profile_picture_url' => fake()->imageUrl(),
            'remember_token' => Str::random(10),
        ];
    }
}

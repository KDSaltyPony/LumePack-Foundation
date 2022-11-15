<?php

namespace LumePack\Foundation\Database\Factories\Auth;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\LumePack\Foundation\Data\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \LumePack\Foundation\Data\Models\Auth\User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'login' => fake()->unique()->safeEmail(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'test',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

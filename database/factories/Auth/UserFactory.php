<?php
/**
 * User class file
 *
 * PHP Version 7.2.19
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Database\Factories\Auth;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\Auth\User;

/**
 * User
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class UserFactory extends Factory
{
    protected $model = User::class;

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
            'password' => null
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

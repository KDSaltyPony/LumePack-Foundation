<?php
/**
 * TaxonomyFactory class file
 *
 * PHP Version 7.2.19
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Database\Factories\Dictionaries;

use Illuminate\Database\Eloquent\Factories\Factory;
use LumePack\Foundation\Data\Models\Dictionaries\Taxonomy;

/**
 * TaxonomyFactory
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class TaxonomyFactory extends Factory
{
    protected $model = Taxonomy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uid' => fake()->unique()->word,
            'name' => fake()->jobTitle(),
            'is_ordered' => false
        ];
    }
}

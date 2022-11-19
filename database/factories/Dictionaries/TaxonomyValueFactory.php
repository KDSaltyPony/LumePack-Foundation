<?php
/**
 * TaxonomyValueFactory class file
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
use LumePack\Foundation\Data\Models\Dictionaries\TaxonomyValue;

/**
 * TaxonomyValueFactory
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class TaxonomyValueFactory extends Factory
{
    protected $model = TaxonomyValue::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uid' => fake()->unique()->word,
            'value' => fake()->jobTitle()
        ];
    }
}

<?php
/**
 * MediaFactory class file
 *
 * PHP Version 7.2.19
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Database\Factories\Storage;

use Illuminate\Database\Eloquent\Factories\Factory;
use LumePack\Foundation\Data\Models\Storage\Media;

/**
 * MediaFactory
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

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
            'comments' => fake()->sentence(10),
            'max_chunk' => rand(1, 32767),
            'mimetype' => '*/*',
            'min_width' => rand(1, 32767),
            'max_width' => rand(1, 32767),
            'min_height' => rand(1, 32767),
            'max_height' => rand(1, 32767)
        ];
    }
}

<?php
/**
 * FileFactory class file
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
use LumePack\Foundation\Data\Models\Storage\File;

/**
 * FileFactory
 *
 * @category Factory
 * @package  LumePack\Foundation\Database\Factories\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->jobTitle()
        ];
        // $table->string('token');
        // $table->string('extension');
        // $table->unsignedInteger('size');
        // $table->foreignId('media_id')->references('id')->on(
        //     'medias'
        // )->onDelete('cascade');
        // $table->timestamps();
        // $table->softDeletes();
    }
}

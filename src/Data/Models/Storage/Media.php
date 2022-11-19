<?php
/**
 * Media class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LumePack\Foundation\Data\Models\BaseModel;
use LumePack\Foundation\Database\Factories\Storage\MediaFactory;

/**
 * Media
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Media extends BaseModel
{
    use HasFactory;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    protected $uid = 'Media';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'deleted_at' ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return MediaFactory::new();
    }

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * Get the Media's Files.
     *
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class)->without('media');
    }

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */
}

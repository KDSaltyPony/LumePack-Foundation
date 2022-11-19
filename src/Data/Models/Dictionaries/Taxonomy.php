<?php
/**
 * Taxonomy class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Dictionaries;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LumePack\Foundation\Data\Models\BaseModel;
use LumePack\Foundation\Database\Factories\Dictionaries\TaxonomyFactory;

/**
 * Taxonomy
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Taxonomy extends BaseModel
{
    use HasFactory;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    protected $log_uid = 'Taxonomy';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [ 'values' ];

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
        return TaxonomyFactory::new();
    }

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * Get the Taxonomy's TaxonomyValues.
     *
     * @return HasMany
     */
    public function taxonomyValues(): HasMany
    {
        return $this->hasMany(TaxonomyValue::class)->without('taxonomy');
    }

    /**
     * Get the Taxonomy's TaxonomyValues.
     *
     * @return HasMany
     */
    public function values(): HasMany
    {
        return $this->taxonomyValues()->orderBy('order');
    }

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */
}

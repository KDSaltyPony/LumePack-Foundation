<?php
/**
 * TaxonomyValue class file
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LumePack\Foundation\Data\Models\BaseModel;
use LumePack\Foundation\Database\Factories\Dictionaries\TaxonomyValueFactory;

/**
 * TaxonomyValue
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class TaxonomyValue extends BaseModel
{
    use HasFactory;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    protected $log_uid = 'TaxonomyValue';

    /**
     * The attribute used to group orders.
     *
     * @var string
     */
    protected $order_grouped_by = 'taxonomy_id';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [ 'taxonomy' ];

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
        return TaxonomyValueFactory::new();
    }

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * Get the TaxonomyValue's Taxonomy.
     *
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(
            Taxonomy::class
        )->without('taxonomyValues')->without('values');
    }

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */

    /**
     * Get the is ordered attribute.
     *
     * @return bool
     */
    public function getIsOrderedAttribute(): bool
    {
        return $this->taxonomy->is_ordered;
    }
}

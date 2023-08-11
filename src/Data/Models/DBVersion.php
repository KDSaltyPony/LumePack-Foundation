<?php
/**
 * DBVersion class file
 *
 * PHP Version 7.2.19
 *
 * @category Trait
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use LumePack\Foundation\Data\Models\BaseModel;

/**
 * DBVersion
 *
 * @category Trait
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class DBVersion extends BaseModel
{
    use DBVersionTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dbversions';
    // TODO: Console comand to add SQL file

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */
}

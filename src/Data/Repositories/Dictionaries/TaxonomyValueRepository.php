<?php
/**
 * TaxonomyValueRepository class file
 *
 * PHP Version 7.2.19
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Repositories\Dictionaries;

use LumePack\Foundation\Data\Repositories\CRUD;

/**
 * TaxonomyValueRepository
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class TaxonomyValueRepository extends CRUD
{
    /**
     * The rows available as filters in the query
     *
     * @var array
     */
    protected $filters = [
        'uid'      => 'uid',
        'value'    => 'value',
        'taxonomy' => 'relation.taxonomy'
    ];

    /**
     * Call parent abstract register method.
     *
     * @inheritdoc
     * @see        parent::register()
     */
    protected function register(array $fields): bool
    {
        return $this->defaultRegister($fields);
    }
}

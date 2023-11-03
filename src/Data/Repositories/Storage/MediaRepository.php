<?php
/**
 * MediaRepository class file
 *
 * PHP Version 7.2.19
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Repositories\Storage;

use LumePack\Foundation\Data\Repositories\CRUD;

/**
 * MediaRepository
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class MediaRepository extends CRUD
{
    /**
     * The rows available as filters in the query
     *
     * @var array
     */
    protected $filters = [
        'id'       => 'id',
        'uid'      => 'uid',
        'name'     => 'name',
        'mimetype' => 'mimetype'
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

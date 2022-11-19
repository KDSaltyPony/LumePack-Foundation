<?php
/**
 * FileRepository class file
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
 * FileRepository
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class FileRepository extends CRUD
{
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

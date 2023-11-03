<?php
/**
 * UserRepository class file
 *
 * PHP Version 7.2.19
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Repositories\Auth;

use LumePack\Foundation\Data\Repositories\CRUD;

/**
 * UserRepository
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class UserRepository extends CRUD
{
    /**
     * The rows available as filters in the query
     *
     * @var array
     */
    protected $filters = [
        'id'        => 'id',
        'login'     => 'login',
        'email'     => 'email',
        'role'      => 'relation.roles',
        'is_active' => 'is_active'
    ];

    /**
     * Set parent CRUD.
     */
    public function __construct()
    {
        parent::__construct(config('crud.user_model'));
    }

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

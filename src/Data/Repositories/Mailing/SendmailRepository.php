<?php
/**
 * SendmailRepository class file
 *
 * PHP Version 7.2.19
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Mailing
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Repositories\Mailing;

use LumePack\Foundation\Data\Repositories\CRUD;

/**
 * SendmailRepository
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Mailing
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class SendmailRepository extends CRUD
{
    /**
     * Modify an existing database item.
     *
     * @param array              $fields  The fields to register
     * @param \DateTimeImmutable $date    The creation date of the to retrieve
     *
     * @return bool
     */
    public function updateWhereToken(array $fields, string $token): bool
    {
        $this->model = $this->model_class::firstWhere('token', $token);

        return $this->register($fields);
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

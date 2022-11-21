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
    public function updateWhereSentAt(array $fields, \DateTimeImmutable $date): bool
    {
        $this->model = $this->model_class::firstWhere(
            'sent_at', $date->format('Y-m-d H:i:s.u')
        );

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
        dump('register bitch');
        return $this->defaultRegister($fields);
    }
}

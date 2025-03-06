<?php
/**
 * LogRepository class file
 *
 * PHP Version 7.2.19
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Log
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Repositories\Log;

use LumePack\Foundation\Data\Repositories\CRUD;

/**
 * LogRepository
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories\Log
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class LogRepository extends CRUD
{
    /**
     * The rows available as filters in the query
     *
     * @var array
     */
    protected $filters = [
        'id'      => '_id',
        'process' => 'process',
        'source'  => 'source',
        'code'    => 'code',
        'data'    => 'data'
        // 'data-is_authenticated' => 'data.is_authenticated',
        // 'data-user_id'          => 'data.user_id',
        // // Model
        // 'data-uid'              => 'data.uid',
        // 'data-table'            => 'data.table',
        // 'data-model'            => 'data.model',
        // 'data-original'         => 'data.original',
        // 'data-original-id'      => 'data.original',
        // 'data-attributes-id     => 'data.attributes',
        // // Request
        // 'data-method'           => 'data.method',
        // 'data-protocol'         => 'data.protocol',
        // 'data-host'             => 'data.host',
        // 'data-port'             => 'data.port',
        // 'data-path'             => 'data.path',
        // 'data-query_string'     => 'data.query_string',
        // 'data-anchor'           => 'data.anchor',
        // // Response
        // 'data-status'           => 'data.status',
        // 'data-reason'           => 'data.reason',
        // // Request & Response
        // 'data-headers'          => 'data.headers',
        // 'data-body'             => 'data.body'
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

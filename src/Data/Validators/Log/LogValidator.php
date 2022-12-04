<?php
/**
 * LogValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Log
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Log;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * LogValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Log
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class LogValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'process' => [ 'string', 'required' ],
        'source' => [ 'string', 'required' ],
        'code' => [ 'string', 'required' ],
        'data' => [ 'array', 'required' ]
    ];
}

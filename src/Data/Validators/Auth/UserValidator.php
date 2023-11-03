<?php
/**
 * UserValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Auth;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * UserValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class UserValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    // TODO: add rules in conf depending on User overide
    protected $rules = [
        'login'       => [ 'required', 'string', 'unique:users,login,:AUTH_ID:' ],
        'email'       => [ 'required', 'email' ],
        'roles'       => [ 'array' ],
        'roles.*'     => [ 'array' ],
        'roles.*.uid' => [ 'required', 'exists:roles,uid', 'distinct' ]
    ];
}

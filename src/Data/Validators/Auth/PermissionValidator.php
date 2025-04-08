<?php
/**
 * PermissionValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Auth;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * PermissionValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class PermissionValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'uid'                => [ 'required', 'unique:permissions,uid' ],
        'name'               => [ 'required' ],
        'permission_type_id' => [ 'required', 'exists:permission_types,id' ]
    ];
}

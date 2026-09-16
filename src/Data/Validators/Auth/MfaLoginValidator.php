<?php
/**
 * MfaLoginValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>, Franz Vetter <fvetter@diatem.net> <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Auth;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * MfaLoginValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>, Franz Vetter <fvetter@diatem.net> <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class MfaLoginValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'mfa_pending_token' => [ 'required', 'string' ],
        'method'            => [ 'required', 'string' ]
    ];

    /**
     * The set of rules of the Validator for resource edition.
     *
     * @var array
     */
    protected $edit_rules = [
        'mfa_pending_token' => [ 'required', 'string' ],
        'method'            => [ 'required', 'string' ],
        'code'              => [ 'required', 'string' ]
    ];
}

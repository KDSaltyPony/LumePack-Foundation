<?php
/**
 * MfaValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   Franz Vetter <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Auth;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * MfaValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Auth
 * @author   Franz Vetter <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class MfaValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'method' => [ 'required', 'string' ]
    ];

    /**
     * The set of rules of the Validator for resource edition.
     *
     * @var array
     */
    protected $edit_rules = [
        'method' => [ 'required', 'string' ],
        'code'   => [ 'required', 'string' ]
    ];
}

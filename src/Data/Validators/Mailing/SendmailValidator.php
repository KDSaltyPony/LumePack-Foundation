<?php
/**
 * SendmailValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Mailing
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Mailing;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * SendmailValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Mailing
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class SendmailValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'subject'            => [ 'required', 'string' ],
        'from.email'         => [ 'required', 'email' ],
        'from.name'          => [ 'required', 'string' ],
        'to'                 => [ 'required', 'array', 'min:1' ],
        'to.*'               => [ 'array' ],
        'to.*.email'         => [ 'required', 'email' ],
        'to.*.name'          => [ 'required', 'string' ],
        'content.template'   => [ 'required', 'string' ],
        'content.attributes' => [ 'required', 'array' ]
    ];
}

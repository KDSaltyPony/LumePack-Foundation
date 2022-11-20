
<?php
/**
 * CRONTaskValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * CRONTaskValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class CRONTaskValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'version'   => [ 'required', 'string', 'unique:dbversions,version' ],
        'sqlscript' => [ 'required', 'string' ],
        'comments'  => [ 'nullable', 'string' ]
    ];
}

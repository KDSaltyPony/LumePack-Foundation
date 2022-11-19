<?php
/**
 * TaxonomyValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Dictionaries;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * TaxonomyValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class TaxonomyValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'uid'        => [ 'required', 'string', 'unique:taxonomies,uid' ],
        'name'       => [ 'required', 'string' ],
        'is_ordered' => [ 'nullable', 'boolean' ]
    ];
}

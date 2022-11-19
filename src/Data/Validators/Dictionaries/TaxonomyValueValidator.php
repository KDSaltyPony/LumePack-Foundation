<?php
/**
 * TaxonomyValueValidator class file
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
 * TaxonomyValueValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Dictionaries
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class TaxonomyValueValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'uid'          => [ 'required', 'string', 'unique:taxonomy_values,uid' ],
        'value'        => [ 'required', 'string' ],
        'order'        => [ 'required', 'integer', 'min:1' ],
        'taxonomy_uid' => [ 'string', 'required', 'exists:taxonomies,uid' ]
    ];
}

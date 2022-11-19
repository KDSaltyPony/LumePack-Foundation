<?php
/**
 * MediaValidator class file
 *
 * PHP Version 7.2.19
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Validators\Storage;

use LumePack\Foundation\Data\Validators\Validator;

/**
 * MediaValidator
 *
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class MediaValidator extends Validator
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [
        'uid'        => [ 'required', 'string', 'unique:media,uid' ],
        'name'       => [ 'required', 'string' ],
        'comments'   => [ 'nullable', 'string' ],
        'mimetype'   => [ 'required', 'string' ],
        'max_chunk'  => [ 'required', 'integer', 'min:1', 'max:32767' ],
        'min_width'  => [ 'nullable', 'integer', 'min:1', 'max:32767' ],
        'max_width'  => [ 'nullable', 'integer', 'min:1', 'max:32767' ],
        'min_height' => [ 'nullable', 'integer', 'min:1', 'max:32767' ],
        'max_height' => [ 'nullable', 'integer', 'min:1', 'max:32767' ]
    ];
}

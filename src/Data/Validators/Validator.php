<?php
/**
 * Validator class file
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

use LumePack\Foundation\Services\ValidatorService;

/**
 * Validator
 * 
 * @category Validator
 * @package  LumePack\Foundation\Data\Validators
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Validator extends ValidatorService
{
    /**
     * The set of rules of the Validator.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * The set of rules of the Validator for resource edition.
     *
     * @var array
     */
    protected $edit_rules = [];

    /**
     * Build a validation and store the results in attributes.
     * 
     * @param array $fields  The fields to validate
     * @param int   $uid     The unique ID of the model (is PUT/PATCH)
     * @param bool  $is_mass Is this a mass validation ?
     */
    public function __construct(
        array $fields,
        ?int $uid = null,
        bool $is_mass = false
    ) {
        if (!is_null($uid) && !empty($this->edit_rules)) {
            $this->rules = $this->edit_rules;
        }

        foreach ($this->rules as $key => $rule) {
            $this->rules[$key] = str_replace(':ID:', $uid, $rule);
        }

        parent::__construct($fields, $is_mass);
    }
}

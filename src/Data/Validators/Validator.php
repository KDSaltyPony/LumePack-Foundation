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
     */
    public function __construct(
        array $fields,
        ?int $uid = null
    ) {
        $uid = $uid?: 'NULL';

        if (!is_null($uid) && !empty($this->edit_rules)) {
            $this->rules = $this->edit_rules;
        }

        foreach ($this->rules as $key => $rule) {
            $this->rules[$key] = preg_replace(
                ($uid === 'NULL'? '/\"?\:ID\:\"?/': '/\:ID\:/'), $uid, $rule
            );
        }

        parent::__construct($fields);
    }
}

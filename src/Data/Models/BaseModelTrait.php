<?php
/**
 * BaseModelTrait class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

/**
 * BaseModelTrait
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait BaseModelTrait
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootEssaiFichesModel()
    {
        static::saved(function (User $model) {
            // TODO: if deleted_at not null and has udi / email or anithing unique => netralize both
        });
    }
}

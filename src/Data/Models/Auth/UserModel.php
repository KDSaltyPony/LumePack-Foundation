<?php
/**
 * UserModel class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Auth;

/**
 * UserModel
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait UserModel
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootEssaiFichesModel()
    {
        static::saved(function (User $model) {
            // TODO: id old password !== new password => send mail!
        });

        static::created(function (User $model) {
            // TODO: send mail!
        });
    }
}

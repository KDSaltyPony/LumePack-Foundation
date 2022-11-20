<?php
/**
 * BaseModelTrait class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use Illuminate\Support\Facades\Hash;

/**
 * BaseModelTrait
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
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
    protected static function bootBaseModelTrait()
    {
        // TODO: log : has trait ?
        static::deleted(function (BaseModel $model) {
            $attrs = $model->getAttributes();

            if (isset($attrs['deleted_at'])) {
                if (isset($attrs['uid'])) {
                    $model->uid = base64_encode($model->uid);
                }

                if (isset($attrs['email'])) {
                    $model->email = Hash::make($model->uid);
                }
            }
        });
    }
}

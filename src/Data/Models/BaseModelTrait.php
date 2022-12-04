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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        static::deleted(function (Model $model) {
            $attrs = $model->getAttributes();

            if (isset($attrs['uid'])) {
                $model->uid = Str::replace('=', '', base64_encode($model->uid));
                $model->uid = Str::replace('+', '', $model->uid);
                $model->uid = Str::replace('/', '', $model->uid);
            }

            if (isset($attrs['login'])) {
                $model->login = Str::replace('=', '', base64_encode($model->login));
                $model->login = Str::replace('+', '', $model->login);
                $model->login = Str::replace('/', '', $model->login);
            }

            if (isset($attrs['email'])) {
                $model->email = Hash::make($model->email);
            }

            if (isset($attrs['password'])) {
                $model->password = null;
            }

            // TODO: instance of user => netralize logs
            $model->saveQuietly();
        });
    }
}

<?php
/**
 * BaseAuthModel class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use LumePack\Foundation\Data\Models\Auth\Permission;

/**
 * BaseAuthModel
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class BaseAuthModel extends Authenticatable
{
    use HasApiTokens, Notifiable, BaseModelTrait, LogTrait;

    /**
     * The uid associated with the model log (default: null). \
     * If null: not logged
     *
     * @var string
     */
    public $log_uid = null;

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */

    public function toArray()
    {
        $class = Str::upper(Str::afterLast(get_class($this), '/'));
        $permissions = Permission::where('uid', 'LIKE', "{$class}_%")->get();

        foreach ($permissions as $permission) {
            $this->hidden[] = Str::camel(Str::after($permission->uid, '_'));
        }

        return parent::toArray();
    }
}
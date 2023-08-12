<?php
/**
 * Permission class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Auth;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\BaseModel;
use LumePack\Foundation\Data\Models\Dictionaries\Types\PermissionType;
use LumePack\Foundation\Database\Factories\Auth\PermissionFactory;

/**
 * Permission
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Permission extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    public $log_uid = 'Permission';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [ /*'type'*/ ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'routes' ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'pivot', 'permission_type_id', 'deleted_at' ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return PermissionFactory::new();
    }

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * Get the Permission's Roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class, 'role_permission'
        )->without('roles');
    }

    /**
     * Get the Permission's Type.
     *
     * @return BelongsTo
     */
    public function permissionType(): BelongsTo
    {
        return $this->belongsTo(PermissionType::class)->without('permissions');
    }

    /**
     * Get the Permission's Type.
     *
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->permissionType();
    }

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */

    /**
     * Get the Permission's routes.
     *
     * @return Collection
     */
    public function getRoutesAttribute(): Collection
    {
        $routes = new Collection();
        $attrs = $this->attributes;
        $controller = Str::after($attrs['uid'], '_');
        $controller = Str::contains($controller, '_')? '_' . Str::after(
            $controller, '_'
        ): '';
        $controller = Str::beforeLast($attrs['uid'], $controller);
        $has_method = false;

        // dd(Route::getRoutes()->getRoutes());
        foreach (Route::getRoutes() as $route) {
            if (in_array('api', $route->action['middleware'])) {
                $uid = ra_to_uid($route);

                // TODO: filter permission request on permission type uid ENDPOINT
                if (Str::startsWith($uid, $controller)) {
                    if ($has_method = Str::contains(
                        $attrs['uid'], Str::after($uid, "{$controller}_")
                    ) || Permission::where(
                        'uid', 'LIKE', "{$uid}%"
                    )->where(
                        'id', '<>', $attrs['id']
                    )->count() === 0) {
                        $routes->add([
                            'uid' => $uid,
                            'uri' => $route->uri
                        ]);
                    }
                }
            }
        }

        if ($has_method) {
            $routes = $routes->filter(function ($route) use ($attrs) {
                return Str::startsWith($attrs['uid'], $route['uid']);
            });
        }

        return $routes;
    }
}

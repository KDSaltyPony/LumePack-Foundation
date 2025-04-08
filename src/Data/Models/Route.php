<?php
/**
 * Route class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Commands
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use Illuminate\Support\Facades\Route as RouteBase;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\Auth\Permission;

/**
 * Route
 *
 * @category Model
 * @package  LumePack\Foundation\Commands
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Route
{
    public static function getRoutes(){
        $out = [];
        $routes = RouteBase::getRoutes();

        foreach ($routes AS $route) {
            if ($route->uri != 'scripts/droits' && Str::startsWith($route->uri, 'api/')) {
                $ligne = [
                    'uri'         => $route->uri,
                    'methods'     => $route->methods,
                    'permissions' => [],
                    'uid'         => ra_to_uid($route)
                ];

                $ligne['uid'] = in_array('GET', $route->methods)? Str::beforeLast($ligne['uid'], '_'): $ligne['uid'];
                $permissions = Permission::where('uid', '=', $ligne['uid'])->get();

                if ($permissions->count() > 0) {
                    foreach($permissions AS $permission){
                        $ligne['permissions'][] = [
                            'id' => $permission->id, 'uid' => $permission->uid
                        ];
                    }
                }

                $out[] = $ligne;
            }
        }

        return $out;
    }
}

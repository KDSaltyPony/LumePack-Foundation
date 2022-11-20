<?php
/**
 * Authenticate class file
 *
 * PHP Version 7.2.19
 *
 * @category Middleware
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\Auth\Permission;
use LumePack\Foundation\Services\ResponseService;

/**
 * Authenticate
 *
 * @category Middleware
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request    The request
     * @param Closure  $next       The controller method passed in routes
     * @param string[] ...$guards  The guard(s) name(s) separeted by points (.)
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if (Route::current()->uri === 'api/auth/dashboard') {
            Route::current()->setParameter('uid', auth()->user()->id);
        }
        // $is_guest = true;
        // $guards = explode('.', $guards);

        // foreach ($guards as $guard) {
        //     if (!$this->auth->guard($guard)->guest()) {
        //         $is_guest = false;
        //     }
        // }
        // dd(auth()->user());

        // if ($is_guest) {
        //     $response = new ResponseService('Non-autorisé.', 401);

        //     $response->setHeader(
        //         'WWW-Authenticate', 'Bearer realm="Access to the API"'
        //     );

        //     return $response->format();
        // }

        if (!is_null(auth()->user())) {
            if (!auth()->user()->is_active) {
                return (new ResponseService(trans('foundation:auth.inactive'), 400))->format();
            } elseif (is_null(auth()->user()->email_verified_at)) {
                return (new ResponseService(trans('foundation:auth.email'), 400))->format();
            }

            // TODO: filter permission request on permission type uid ENDPOINT
            $method = ra_to_uid(Route::current());
            $permission = Permission::where(
                'uid', 'LIKE', "{$method}%"
            )->get();

            if ($permission->isEmpty()) {
                $method = Str::beforeLast($method, '_');
                $permission = Permission::where(
                    'uid', 'LIKE', "{$method}%"
                )->get();
            }

            if ($permission->isNotEmpty()) {
                $permission = Permission::join('role_permission as rperm', function ($join) {
                    $join->on('rperm.permission_id', '=', 'permissions.id');
                    $join->whereIn(
                        'rperm.role_id', auth()->user()->roles->pluck('id')->toArray()
                    );
                })->whereIn(
                    'uid', $permission->pluck('uid')->toArray()
                )->first();

                if (is_null($permission)) {
                    return (new ResponseService(trans('foundation:auth.403'), 403))->format();
                }

                $filters = Str::of(Str::after(
                    $permission->uid, $method
                ))->explode('_');

                config([ 'auth.filters' => $filters->toArray() ]);
            }
        }

        return $next($request);
    }
}

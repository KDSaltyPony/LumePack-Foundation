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
        // $has_rights = false;
        // $guards = explode('.', $guards);
        // $method = $request->route()[1]['uses'];
        // // dd($request->route());
        // $method = str_replace('App\\Http\\Controllers\\', '', $method);
        // $method = str_replace('\\', '', $method);
        // $method = str_replace('Controller', '', $method);
        // $method = str_replace('@', '_', $method);
        // $method = strtolower($method);

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

        // if (!auth()->user()->actif || auth()->user()->supprime) {
        //     return (
        //         new ResponseService('Utilisateur inactif ou supprimé.', 403)
        //     )->format();
        // }
        // // dd($method);

        // if (
        //     !is_null(auth()->user()->entite) &&
        //     Droit::firstWhere('uid', 'LIKE', "{$method}%")
        // ) {
        //     foreach (auth()->user()->profils as $profil) {
        //         foreach ($profil->droits as $droit) {
        //             if (str_starts_with($droit->uid, $method)) {
        //                 $has_rights = true;

        //                 $filters = explode('_', $droit->uid);
        //                 array_shift($filters);
        //                 array_shift($filters);

        //                 // $request->merge([ 'auth_filters' => $filters ]);
        //                 config([ 'auth.filters' => $filters ]);
        //             }
        //         }
        //     }

        //     if (!$is_open && !$has_rights) {
        //         return (new ResponseService('Non-autorisé.', 403))->format();
        //     }
        // }

        return $next($request);
    }
}

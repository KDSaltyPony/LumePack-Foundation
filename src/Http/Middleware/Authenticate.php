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

use Illuminate\Auth\Middleware\Authenticate as Middleware;

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
    // /**
    //  * Get the path the user should be redirected to when they are not authenticated.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return string|null
    //  */
    // protected function redirectTo($request)
    // {
    //     if (! $request->expectsJson()) {
    //         return route('login');
    //     }
    // }
}

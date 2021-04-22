<?php
/**
 * DataCheck class file
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
use LumePack\Foundation\Services\ResponseService;
use Illuminate\Http\Request;

/**
 * DataCheck
 * 
 * @category Middleware
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class DataCheck
{
    /**
     * The methods that should be tested
     * (allow the middleware to be set on named routes)
     * 
     * @var array $_methods
     */
    private $_methods = ['POST', 'PUT', 'PATCH'];

    /**
     * Handle an incoming request.
     *
     * @param Request $request The request to validate
     * @param Closure $next    The controller method passed in routes
     * 
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->getMethod(), $this->_methods)) {
            // if ($request->getContent()) {
            //     return (new ResponseService(
            //         'Empty form-data / x-www-form-urlencoded', 204
            //     ))->format();
            // }
        }

        return $next($request);
    }
}

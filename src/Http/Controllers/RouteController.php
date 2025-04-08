<?php
/**
 * RouteController class file
 *
 * PHP Version 7.2.19
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LumePack\Foundation\Http\Controllers\BaseController;
use LumePack\Foundation\Data\Models\Route;

/**
 * RouteController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class RouteController extends BaseController
{
    /**
     * Method called by the /remote_admin/routes URL in GET.
     *
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function routes(Request $request): JsonResponse
    {
        $this->setResponse(Route::getRoutes());

        return $this->response->format();
    }
}

<?php
/**
 * UserController class file
 *
 * PHP Version 7.2.19
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LumePack\Foundation\Http\Controllers\BaseController;

/**
 * UserController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class UserController extends BaseController
{
    /**
     * Method called by the /api/auth/user/email/{token} URL in POST.
     *
     * @param string  $token   The valid token
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function validate(string $token, Request $request): JsonResponse
    {
        $user_model = config('crud.user_model');
        $user = $user_model::firstWhere('email_token', $token);

        $this->setResponse(trans('foundation::user.unverified'), 500);

        if (!is_null($user)) {
            $user->email_verified_at = new \DateTime();
            $user->save();

            $this->setResponse(trans('foundation::user.login'), 200);
        }

        return $this->response->format();
    }
}

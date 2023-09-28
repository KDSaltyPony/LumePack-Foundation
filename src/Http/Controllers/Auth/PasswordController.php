<?php
/**
 * PasswordController class file
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
 * PasswordController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class PasswordController extends BaseController
{
    /**
     * Method called by the /api/auth/pwd/forgot URL in POST.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function forgot(Request $request): JsonResponse
    {
        $user_model = config('crud.user_model');
        $user = $user_model::firstWhere('login', $request->get('login'));
        $this->setResponse(trans('foundation::pwd.error'), 500);

        if (!is_null($user)) {
            $user->pwd_token = $user_model::pwdTokenize();
            $user->save();

            $this->setResponse(trans('foundation::pwd.email'));
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/pwd/{token} URL in POST.
     *
     * @param string  $token   The valid token
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function mailRenew(string $token, Request $request): JsonResponse
    {
        $user_model = config('crud.user_model');
        $user = $user_model::firstWhere('pwd_token', $token);

        if (is_null($user)) {
            $duration_min = env('PWD_TOKEN_VALIDITY') + 1;
        } else {
            $duration = (new \DateTime($user->pwd_token_created_at))->diff(new \DateTime);
            $duration_min = $duration->days * 24 * 60;
            $duration_min += $duration->h * 60;
            $duration_min += $duration->i;
        }

        $this->setResponse(trans('foundation::pwd.token'), 500);

        if ($duration_min <= env('PWD_TOKEN_VALIDITY')) {
            $user->password = $request->get('password');
            $user->pwd_token = null;

            if ($user->save()) {
                $this->setResponse(trans('foundation::pwd.renew'), 200);
            }
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/pwd/renew URL in POST.
     *
     * @param string  $jeton   The valid token
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function renew(Request $request): JsonResponse
    {
        $this->setResponse(trans('foundation::pwd.error'), 500);

        $request->user()->password = $request->get('new_password');

        if ($request->user()->save()) {
            $this->setResponse(trans('foundation::pwd.renew'), 200);
        }

        return $this->response->format();
    }
}

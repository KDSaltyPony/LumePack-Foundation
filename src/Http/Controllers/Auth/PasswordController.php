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
use LumePack\Foundation\Data\Models\Auth\User;
use LumePack\Foundation\Http\Controllers\BaseController;
use Illuminate\Support\Str;

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
     * Method called by the /api/auth/pwd/forgot URL in GET.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function forgot(Request $request): JsonResponse
    {
        $this->setResponse(trans('pwd.error'), 500);

        $this->_tokenize(User::firstWhere(
            'login', $request->get('login')
        ));

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/pwd/{token} URL in GET.
     *
     * @param string  $token   The valid token
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function mailRenew(string $token, Request $request): JsonResponse
    {
        $user = User::firstWhere('pwd_token', $token);

        if (is_null($user)) {
            $duration_min = env('PWD_TOKEN_VALIDITY') + 1;
        } else {
            $duration = (new \DateTime($user->pwd_token_created_at))->diff(new \DateTime);
            $duration_min = $duration->days * 24 * 60;
            $duration_min += $duration->h * 60;
            $duration_min += $duration->i;
        }

        $this->setResponse(trans('pwd.token'), 500);

        if ($duration_min <= env('PWD_TOKEN_VALIDITY')) {
            $user->password = $request->get('password');
            $user->pwd_token = null;
            $user->pwd_token_created_at = null;

            if ($user->save()) {
                $this->setResponse(trans('pwd.renew'), 200);
            }
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/pwd/renew URL in GET.
     *
     * @param string  $jeton   The valid token
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function renew(Request $request): JsonResponse
    {
        $this->setResponse(trans('pwd.error'), 500);

        $request->user()->password = $request->get('new_password');

        if ($request->user()->save()) {
            $this->setResponse(trans('pwd.renew'), 200);
        }

        return $this->response->format();
    }

    private function _tokenize(User $user): void
    {
        $user->pwd_token = $this->_token();
        $user->pwd_token_created_at = new \DateTime();

        // TODO: mail
        // count(Mail::failures()) === 0 $this->setResponse(trans('pwd.email_error'), 500)

        if ($user->save()) {
            $this->setResponse(trans('pwd.email'));
        }
    }

    private function _token(): string
    {
        do {
            $token = Str::random(32);
        } while (!is_null(User::firstWhere('pwd_token', $token)));

        return $token;
    }
}

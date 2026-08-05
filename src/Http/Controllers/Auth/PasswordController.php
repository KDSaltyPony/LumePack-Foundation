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

use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\Token;
use LumePack\Foundation\Http\Controllers\BaseController;
use LumePack\Foundation\Mail\BaseMail;

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

        if (env('IS_LOGIN_KS', false)) {
            $user = $user_model::firstWhere('login', $request->login);
        } else {
            $user = $user_model::firstWhere(DB::raw('LOWER(login)'), Str::lower($request->get('login')));
        }

        $this->setResponse(trans('foundation::pwd.error'), 500);

        if (!is_null($user) && !is_null($user->email)) {
            $token_string = Token::generateTokenString();
            $duration_min = env('PWD_TOKEN_VALIDITY');
            $creation_date = new DateTime();

            $user->pwdTokens()->create([
                'purpose'    => 'pwd_forgot',
                'name'       => "pwd_forgot-{$creation_date->getTimestamp()}",
                'token'      => $token_string,
                'expires_at' => $creation_date->modify("+{$duration_min} minutes")
            ]);

            Mail::send(new BaseMail('foundation::emails.auth.forgot', [
                'user'             => $user,
                'token'            => $token_string,
                'token_expires_at' => $creation_date,
                'subject'          => trans('foundation::mail.subject_auth_forgot')
            ]));

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
        $token = Token::firstWhere('token', hash('sha256', $token));
        $duration = (new \DateTime())->diff($token->expires_at);

        $this->setResponse(trans('foundation::pwd.token'), 500);

        if (
            !is_null($token) &&
            intval($duration->format('%R%i')) >= 0 &&
            $token->tokenable::class === config('crud.user_model')
        ) {
            $token->tokenable->password = $request->get('password');

            if ($token->tokenable->save()) {
                $this->setResponse(trans('foundation::pwd.renew'), 200);
            }
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/pwd/renew URL in POST.
     *
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

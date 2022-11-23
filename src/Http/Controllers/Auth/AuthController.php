<?php
/**
 * AuthController class file
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

use LumePack\Foundation\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;
use LumePack\Foundation\Data\Models\Auth\AccessToken;
use LumePack\Foundation\Data\Models\Auth\User;

/**
 * AuthController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class AuthController extends BaseController
{
    /**
     * Method called by the /api/auth/login URL in POST.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $user = User::where('login', $request->login)->first();

        if (
            !$user || !is_null($user->deleted_at) ||
            !Hash::check($request->password, $user->password)
        ) {
            $this->setResponse(trans('foundation::auth.failed'), 400);
        } elseif (!$user->is_active) {
            $this->setResponse(trans('foundation::auth.inactive'), 400);
        } elseif (config('auth.is_mail_locked') && is_null($user->email_verified_at)) {
            $this->setResponse(trans('foundation::auth.email'), 400);
        } else {
            foreach ($user->tokens()->getResults() as $access_token) {
                if (
                    Hash::check($request->server('HTTP_USER_AGENT'), $access_token->name) ||
                    (!is_null($access_token->expires_at) && new \DateTime($access_token->expires_at) < new \DateTime())
                ) {
                    $access_token->delete();
                }
            }

            $this->setResponse(
                $this->setTokenBody($user->createToken(
                    Hash::make($request->server('HTTP_USER_AGENT')), [ '*' ],
                    (
                        is_null(config('sanctum.expiration'))?
                            null:
                            now()->addMinutes(config('sanctum.expiration'))
                    )
                ), $user)
            );
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/refresh URL in GET.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $this->setResponse(
            $this->setTokenBody($request->user()->createToken(
                Hash::make($request->server('HTTP_USER_AGENT')), [ '*' ],
                (
                    is_null(config('sanctum.expiration'))?
                        null:
                        now()->addMinutes(config('sanctum.expiration'))
                )
            ))
        );

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/logout URL in GET.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $this->setResponse(trans('foundation::auth.logout'));

        return $this->response->format();
    }

    /**
     * Helper function to format the response with the token.
     *
     * @param NewAccessToken $token The token
     *
     * @return array
     */
    protected function setTokenBody(NewAccessToken $token, User $user = null): array
    {
        return [
            'token'      => $token->plainTextToken,
            'token_type' => 'bearer',
            'expires_at' => (
                is_null($token->accessToken->expires_at)? null: (
                    new \DateTime($token->accessToken->expires_at)
                )->format('Y-m-d\TH:i:s.u\Z')
                // 2022-11-16T13:18:20.000000Z
                // 2022-11-16T13:30:54.000000Z
            ),
            'user'       => (is_null($user)? auth()->user(): $user)
        ];
    }
}

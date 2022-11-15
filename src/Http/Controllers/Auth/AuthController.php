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
     * Method called by the /api/auth/login URL in GET.
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
            $this->setResponse(trans('auth.failed'), 400);
        } elseif (!$user->is_active) {
            $this->setResponse(trans('auth.inactive'), 400);
        } elseif (is_null($user->email_verified_at)) {
            $this->setResponse(trans('auth.email'), 400);
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
                $this->_setTokenBody($user->createToken(
                    Hash::make($request->server('HTTP_USER_AGENT'))
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
            $this->_setTokenBody($request->user()->createToken(
                Hash::make($request->server('HTTP_USER_AGENT'))
            ))
        );

        return $this->response->format();
    }

    // public function profil(): JsonResponse
    // {
    //     $this->setResponse(auth()->user());

    //     return $this->response->format();
    // }

    // public function profilEdit(Request $request): JsonResponse
    // {
    //     $request->merge([ 'entite_id' => auth()->user()->entite->id ]);

    //     return parent::edit(auth()->user()->id, $request);
    // }

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
        $this->setResponse(trans('auth.logout'));

        return $this->response->format();
    }

    /**
     * Helper function to format the response with the token.
     *
     * @param NewAccessToken $token The token
     *
     * @return array
     */
    private function _setTokenBody(NewAccessToken $token, User $user = null): array
    {
        return [
            'token'      => $token->plainTextToken,
            'token_type' => 'bearer',
            'expires_at' => (
                is_null($token->accessToken->expires_at)? null: (
                    new \DateTime($token->accessToken->expires_at)
                )->getTimestamp()
            ),
            'user'       => (is_null($user)? auth()->user(): $user)
        ];
    }
}

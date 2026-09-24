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

use App\Data\Models\Auth\Role;
use LumePack\Foundation\Data\Models\HasMfa;
use LumePack\Foundation\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
     * Method called by the /api/auth/login URL in POST.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $user = $this->findUser($request->login);

        if (
            !$user || !is_null($user->deleted_at) ||
            !Hash::check($request->password, $user->password)
        ) {
            $this->setResponse(trans('foundation::auth.failed'), 400);
        } elseif (!$user->is_active) {
            $this->setResponse(trans('foundation::auth.inactive'), 400);
        } elseif (
            config('auth.is_mail_locked') && is_null($user->email_verified_at)
        ) {
            $this->setResponse(trans('foundation::auth.email'), 400);
        } else {
            if ($user->isMfaSetupMendatory() || $user->isMfaSet()) {
                $this->setResponse([
                    'is_mfa_set'        => $user->isMfaSet(),
                    'mfa_methods'       => $user->isMfaSet()? $user->isEnabledMfaMethods(): config('mfa.methods'),
                    'mfa_pending_token' => $user->pendingTokenCreate()
                ]);
            } else {
                $this->pruneTokens($request, $user);
                $this->setResponse($this->setTokenBody($request, $user));
            }
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/login/mfa URL in PUT.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function mfa(Request $request): JsonResponse
    {
        $method = $request->get('method');
        $token = $request->input('mfa_pending_token');
        $user = HasMfa::pendingTokenRetriveUser($token);

        $this->setResponse(trans('foundation::mfa.user_not_found'), 401);

        if (!is_null($user)) {
            $this->setResponse(trans('foundation::mfa.method_unknown'), 422);

            if (in_array($method, config('mfa.methods'), true)) {
                $record = $user->mfaMethod($method);

                if (!is_null($record)) {
                    $this->setResponse(
                        trans('foundation::mfa.code_invalid'), 422
                    );

                    if ($user->verify(
                        $method, $request->get('code'), $record->secret
                    )) {
                        if (!$record->is_enabled) {
                            $record->is_enabled = true;
                            $record->confirmed_at = now();
                            $record->save();
                        }

                        $user->pendingTokenDelete($token);

                        $this->pruneTokens($request, $user);
                        $this->setResponse(
                            $this->setTokenBody($request, $user)
                        );
                    } else {
                        $user->pendingTokenFail($token);
                    }
                }
            }
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

        $this->setResponse($this->setTokenBody($request));

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
     * Helper function to find the user with the login.
     *
     * @param string $login The login of the user to find
     *
     * @return User|null
     */
    protected function findUser(string $login): ?User
    {
        $user_model = config('crud.user_model');

        if (env('IS_LOGIN_KS', false)) {
            $user = $user_model::where('login', $login);
        } else {
            $user = $user_model::where(DB::raw('LOWER(login)'), Str::lower($login));
        }

        $relations = empty($relations)? config('query.relations', []): $relations;

        foreach ($relations as $relation) {
            $user->with($relation);
        }

        return $user->first();
    }

    /**
     * Helper function to clean outdated user's tokens.
     *
     * @param Request $request The request (some token parameters are based on it)
     * @param User    $user    The user
     *
     * @return void
     */
    protected function pruneTokens(Request $request, User $user): void
    {
        foreach ($user->tokens()->getResults() as $access_token) {
            if (
                (
                    is_null($access_token->expires_at) &&
                    Hash::check(
                        $request->server('HTTP_USER_AGENT'),
                        $access_token->name
                    )
                ) || (
                    !is_null($access_token->expires_at) &&
                    $access_token->expires_at < new \DateTime()
                )
            ) {
                $access_token->delete();
            }
        }
    }

    /**
     * Helper function to format the response with the token.
     *
     * @param Request   $request The request (some token parameters are based on it)
     * @param User|null $user    The user (default auth)
     *
     * @return array
     */
    protected function setTokenBody(Request $request, User $user = null): array
    {
        $user = is_null($user)? auth()->user(): $user;
        $token = $user->createToken(
            Hash::make($request->server('HTTP_USER_AGENT')),
            [ '*' ],
            (
                is_null(config('sanctum.expiration_override'))? (
                    is_null(config('sanctum.expiration'))?
                        null: now()->addMinutes(config('sanctum.expiration'))
                ): now()->addMinutes(config('sanctum.expiration_override'))
            )
        );

        return [
            'token'      => $token->plainTextToken,
            'token_type' => 'bearer',
            'expires_at' => (
                is_null($token->accessToken->expires_at)?
                    null: $token->accessToken->expires_at
                // 2022-11-16T13:18:20.000000Z
                // 2022-11-16T13:30:54.000000Z
            ),
            'user'       => $user
        ];
    }
}

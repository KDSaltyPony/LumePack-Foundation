<?php
/**
 * MfaMethodController class file
 *
 * PHP Version 7.2.19
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Auth
 * @author   Franz Vetter <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Controllers\Auth;

use LumePack\Foundation\Data\Models\Auth\MfaMethod;
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
 * MfaMethodController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Auth
 * @author   Franz Vetter <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class MfaMethodController extends BaseController
{
    /**
     * The retrived user (auth or via the pending token).
     *
     * @var User $user
     */
    protected $user = null;

    /**
     * Method called by the /api/auth/mfa or api/auth/login/mfa URL in POST.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function establish(Request $request): JsonResponse
    {
        $method = $request->get('method');

        $this->setUser($request);
        $this->setResponse(trans('foundation::mfa.user_not_found'), 401);

        if (!is_null($this->user)) {
            $this->setResponse(trans('foundation::mfa.method_unknown'), 422);

            if (in_array($method, config('mfa.methods'), true)) {
                $record = $this->user->mfaMethod($method);

                $this->setResponse(
                    trans('foundation::mfa.method_active'), 409
                );

                if (is_null($record) || !$record->enabled) {
                    $this->user->mfaMethods()->createOrFirst([ 'method' => $method ], [
                        'secret'       => ($method === 'totp')? $this->user->totpSecret(): null,
                        'enabled'      => false,
                        'confirmed_at' => null
                    ]);

                    $this->setTotp($method);
                } elseif (
                    !is_null($record) &&
                    $record->enabled &&
                    $request->has('mfa_pending_token')
                ) {
                    $this->setTotp($method);
                }
            }
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/mfa URL in PUT.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function enable(Request $request): JsonResponse
    {
        $method = $request->get('method');

        $this->setUser($request);
        $this->setResponse(trans('foundation::mfa.user_not_found'), 401);

        if (!is_null($this->user)) {
            $this->setResponse(trans('foundation::mfa.method_unknown'), 422);

            if (in_array($method, config('mfa.methods'), true)) {
                $record = $this->user->mfaMethod($method);

                // $this->setResponse(trans('foundation::mfa.method_active'), 409);
                // if (!$record) {
                //     return $this->respond(
                //         ['message' => "Aucune procédure d'activation en cours pour cette méthode, relancez /{$method}/setup."],
                //         422
                //     );
                // }

                if (!is_null($record)) {
                    $this->setResponse(
                        trans('foundation::mfa.code_invalid'), 422
                    );

                    if ($this->user->verify(
                        $method, $request->get('code'), $record->secret
                    )) {
                        $record->enabled = true;
                        $record->confirmed_at = now();
                        $record->save();

                        $this->setResponse(
                            trans('foundation::mfa.method_enabled')
                        );
                    }
                }
            }
        }

        return $this->response->format();
    }

    /**
     * Method called by the /api/auth/mfa URL in DELETE.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function disable(Request $request): JsonResponse
    {
        $method = $request->get('method');

        $this->setUser($request);
        $this->setResponse(trans('foundation::mfa.user_not_found'), 401);

        if (!is_null($this->user)) {
            $this->setResponse(trans('foundation::mfa.method_unknown'), 422);

            if (in_array($method, config('mfa.methods'), true)) {
                $this->setResponse(trans('foundation::mfa.mendatory'), 422);

                if ($this->user->canDisableMfa()) {
                    $record = $this->user->mfaMethod($method);

                    $this->setResponse(
                        trans('foundation::mfa.method_inactive'), 409
                    );

                    if (!is_null($record) && $record->enabled) {
                        $record->enabled = false;
                        $record->confirmed_at = null;
                        $record->save();

                        $this->setResponse(
                            trans('foundation::mfa.method_disabled')
                        );
                    }
                }
            }
        }

        return $this->response->format();
    }

    /**
     * Helper function to retrive a MFA method for a user (auth default).
     *
     * @param Request|null $request The request
     *
     * @return void
     */
    protected function setUser(Request $request)
    {
        $this->user = auth()->hasUser()?
            auth()->user():
            HasMfa::pendingTokenRetriveUser(
                $request->get('mfa_pending_token')
            );
    }

    /**
     * Helper function to create a TOTP answer based on the method.
     *
     * @param string $method The method to setup
     *
     * @return void
     */
    protected function setTotp(string $method): void
    {
        $secret = $this->user->mfaMethod($method)->secret;

        switch ($method) {
            case 'totp':
                $this->setResponse([
                    'secret'   => $secret,
                    'totp_url' => $this->user->totpQrCodeUrl($this->user->login, $secret)
                ]);
                break;

            case 'email':
                $this->user->emailCode();
                $this->setResponse(trans('foundation::mfa.email_sent'));
                break;
        }
    }
}

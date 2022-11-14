<?php
/**
 * LoginController class file
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

use App\Data\Models\User;
use LumePack\Foundation\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * LoginController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class LoginController extends BaseController
{
    /**
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        dd($request->server('HTTP_USER_AGENT'));
        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        //     'device_name' => 'required',
        // ]);

        // $user = User::where('login', $request->login)->first();

        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     throw ValidationException::withMessages([
        //         'email' => ['The provided credentials are incorrect.'],
        //     ]);
        // }

        // return $user->createToken($request->device_name)->plainTextToken;
        // auth()->attempt($request->only([ 'login', 'password' ]));
        // $credentials = $request->only([ 'login' ]);

        // if (!$token = app('auth')->attempt($credentials)) {
        //     $this->setResponse('Identifiants invalides.', 400);
        // } else {
        //     $this->setResponse($this->_setTokenBody($token));
        // }

        // if (auth()->check()) {
        //     // if (!auth()->user()->actif || auth()->user()->supprime) {
        //     //     app('auth')->invalidate();

        //     //     $this->setResponse('Utilisateur inactif ou supprimé.', 403);
        //     // }
        // }

        // return $this->response->format();
    }

    public function refresh(): JsonResponse
    {
        $this->setResponse($this->_setTokenBody(app('auth')->refresh()));

        return $this->response->format();
    }

    public function profil(): JsonResponse
    {
        $this->setResponse(auth()->user());

        return $this->response->format();
    }

    public function profilEdit(Request $request): JsonResponse
    {
        $request->merge([ 'entite_id' => auth()->user()->entite->id ]);

        return parent::edit(auth()->user()->id, $request);
    }

    /**
     * Deconnexion
     *
     * Permet d'invalider un token JWT.
     *
     * @response {
     *     "meta": { ... },
     *     "data": true
     * }
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        app('auth')->parseToken()->invalidate();

        $this->setResponse('Utilisateur déconnecté avec succès');

        return $this->response->format();
    }

    /**
     * Helper function to format the response with the token.
     *
     * @param string $token The token
     *
     * @return array
     */
    private function _setTokenBody(string $token): array
    {
        return [
            // 'jeton'       => $token,
            // 'jeton_type'  => 'bearer',
            // 'expire_dans' => app('auth')->factory()->getTTL() * 60,
            // 'utilisateur' => auth()->user()
        ];
    }
}

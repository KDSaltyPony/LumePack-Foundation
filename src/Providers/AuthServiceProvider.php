<?php
/**
 * AuthServiceProvider class file
 *
 * PHP Version 7.2.19
 *
 * @category Provider
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use LumePack\Foundation\Data\Models\Auth\User;

/**
 * AuthServiceProvider
 *
 * @category Provider
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app['auth']->extend('jwt', function ($app, $name, array $config)
        // {
        //     // try {
        //     //     if (!($user = JWTAuth::parseToken()->authenticate())) {
        //     //         return response()->json(['error' => 'user_not_found'], 404);
        //     //     }
        //     // } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        //     //     return response()->json(['error' => 'token_expired'], $e->getStatusCode());
        //     // } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        //     //     return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
        //     // } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
        //     //     return response()->json(['error' => 'token_absent'], $e->getStatusCode());
        //     // }
        //     dd($config);
        //     $user = JWTAuth::parseToken()->authenticate();

        //     // if (!is_null($user) && (!$user->actif || $user->supprime)) {
        //         $user = null;
        //     //     JWTAuth::parseToken()->invalidate();
        //     // }

        //     return $user;
        // });
        // $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
        //     dd($app);
        //     // return new JWTGuard(Auth::createUserProvider($config['provider']));
        // });
        // $this->app['auth']->viaRequest('bearer', [ $this, 'jwt' ]);
        // $this->app['auth']->viaRequest('jwt', [ $this, 'jwt' ]);
        // dd($this);
        // $this->app['auth']->viaRequest('token', [ $this, 'token' ]);
    }

    // /**
    //  * Bearer auth, checking the JWT token in Authorization header.
    //  * Set the user if authorized.
    //  *
    //  * @param Request $request The request to validate
    //  *
    //  * @return Utilisateur|null
    //  */
    // public function jwt(Request $request): ?Utilisateur
    // {
    //     // try {
    //     //     if (!($user = JWTAuth::parseToken()->authenticate())) {
    //     //         return response()->json(['error' => 'user_not_found'], 404);
    //     //     }
    //     // } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
    //     //     return response()->json(['error' => 'token_expired'], $e->getStatusCode());
    //     // } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
    //     //     return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
    //     // } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
    //     //     return response()->json(['error' => 'token_absent'], $e->getStatusCode());
    //     // }
    //     // JWTAuthException
    //     // $user = JWTAuth::parseToken()->authenticate();

    //     // if (!is_null($user) && (!$user->actif || $user->supprime)) {
    //     //     $user = null;
    //     //     JWTAuth::parseToken()->invalidate();
    //     // }
    //     // try {
    //     //     // attempt to verify the credentials and create a token for the user
    //     //     if (! $token = JWTAuth::attempt($credentials)) {
    //     //         return response()->json(['error' => 'invalid_credentials'], 401);
    //     //     }
    //     // } catch (JWTException $e) {
    //     //     // something went wrong whilst attempting to encode the token
    //     //     return response()->json(['error' => 'could_not_create_token'], 500);
    //     // }
    //     // dd(app('auth'));

    //     return JWTAuth::parseToken()->invalidate();
    //     // return $user;
    // }

    /**
     * Token auth, checking the DB token in Authorization header.
     * Set the user if authorized.
     *
     * @param Request $request The request to validate
     *
     * @return Utilisateur|null
     */
    public function token(Request $request): ?User
    {
    //     if ($request->header('Authorization')) {
    //         $head = explode(' ', $request->header('Authorization'));

    //         if (strtolower($head[0]) === 'bearer') {
    //             return Utilisateur::where('api_token', $head[1])->first();
    //         }
    //     }

        return null;
    }
}

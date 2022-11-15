<?php

use Illuminate\Routing\RouteParameterBinder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace(
    'LumePack\\Foundation\\Http\\Controllers'
)->group(function () {
    Route::prefix('auth')->namespace('Auth')->middleware(
        'auth:sanctum'
    )->group(function () {
        Route::controller('AuthController')->middleware(
            'dataValidation:auth.auth,lume_pack.foundation'
        )->group(function () {
            Route::withoutMiddleware('auth:sanctum')->post('login', 'login');
            Route::get('refresh', 'refresh');
            Route::get('logout', 'logout');
        });

        Route::prefix('dashboard')->controller('UserController')->middleware(
            'dataValidation:auth.user,lume_pack.foundation'
        )->group(function () {
            // Route::get('/', 'show')->defaults('uid', Request::user()->id);
            Route::get('/', 'show');
        });
    });
});

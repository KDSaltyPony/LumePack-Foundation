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
        'lpfauth:sanctum'
    )->group(function () {
        Route::controller('AuthController')->middleware(
            'dataValidation:auth.auth,lume_pack.foundation'
        )->group(function () {
            Route::withoutMiddleware('lpfauth:sanctum')->post('login', 'login');
            Route::get('refresh', 'refresh');
            Route::get('logout', 'logout');
        });

        Route::prefix('dashboard')->controller('UserController')->middleware(
            'dataValidation:auth.user,lume_pack.foundation'
        )->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'edit');
        });

        Route::prefix('pwd')->controller(
            'PasswordController'
        )->group(function () {
            Route::withoutMiddleware('lpfauth:sanctum')->middleware(
                'dataValidation:auth.passwordForgot,lume_pack.foundation'
            )->post('forgot', 'forgot');
            // 6MRUa75RvqcMN8oLWyd3i1BztisJGxHy
            Route::middleware(
                'dataValidation:auth.passwordRenew,lume_pack.foundation'
            )->post('renew', 'renew');
            Route::withoutMiddleware('lpfauth:sanctum')->middleware(
                'dataValidation:auth.passwordForgotRenew,lume_pack.foundation'
            )->post('{token}', 'mailRenew');
        });

        // Route::prefix('role')->controller('RoleController')->middleware(
        //     'dataValidation:auth.role,lume_pack.foundation'
        // )->group(function () {
        //     Route::get('/', 'list');
        //     Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
        //     Route::post('/', 'add');
        //     Route::put('{uid}', 'edit');
        //     Route::delete('{uid}', 'delete')->where([ 'uid' => '[0-9]+' ]);
        // });

        // Route::prefix('permission')->controller('PermissionController')->middleware(
        //     'dataValidation:auth.permission,lume_pack.foundation'
        // )->group(function () {
        //     Route::get('/', 'list');
        //     Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
        //     Route::post('/', 'add');
        //     Route::put('{uid}', 'edit');
        //     Route::delete('{uid}', 'delete')->where([ 'uid' => '[0-9]+' ]);
        // });
    });

    // Route::prefix('storage')->namespace('Storage')->middleware(
    //     'lpfauth:sanctum'
    // )->group(function () {
    Route::prefix('storage')->namespace('Storage')->group(function () {
        Route::prefix('media')->controller('MediaController')->middleware(
            'dataValidation:storage.media,lume_pack.foundation'
        )->group(function () {
            Route::get('/', 'list');
            Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
            Route::post('/', 'add');
            Route::put('{uid}', 'edit');
            Route::delete('{uid}', 'delete')->where([ 'uid' => '[0-9]+' ]);
        });

        Route::prefix('file')->controller('FileController')->middleware(
            'dataValidation:storage.file,lume_pack.foundation'
        )->group(function () {
            Route::get('/', 'list');
            Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
            Route::post('/', 'add');
            Route::put('{uid}', 'edit');
            Route::delete('{uid}', 'delete')->where([ 'uid' => '[0-9]+' ]);
            Route::post('upload', 'upload');
            Route::get('stream/{token}.{action}', 'stream')->where([
                'token' => '[0-9A-z]+', 'action' => 'show|down'
            ]);
        });
    });
});

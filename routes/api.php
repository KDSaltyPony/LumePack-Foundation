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

// $router->options('{all:.*}', ['middleware' => 'cors.options', function() {
//     return response('');
// }]);

//
//
// TO DELETE
//
//
// Route::get('tests', function (Request $request) {
// });

Route::prefix('auth')->namespace('Auth')->middleware(
    'lpfauth:sanctum'
)->group(function () {
    // routes relative to the current user
    Route::prefix('/')->controller('UserController')->middleware(
        'dataValidation:auth.user,lume_pack.foundation'
    )->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'edit');
    });

    // routes relative to standard login
    Route::controller('AuthController')->middleware(
        'dataValidation:auth.auth,lume_pack.foundation'
    )->group(function () {
        Route::withoutMiddleware('lpfauth:sanctum')->post('login', 'login');
        Route::get('refresh', 'refresh');
        Route::get('logout', 'logout');
    });

    // routes relative to mfa mangment for users logging in
    Route::prefix('login')->controller('MfaController')->middleware(
        'dataValidation:auth.mfa_login,lume_pack.foundation'
    )->group(function () {
        Route::withoutMiddleware('lpfauth:sanctum')->post('mfa', 'establish');
    });

    // routes relative to mfa mangment for users logging in
    Route::prefix('login')->controller('AuthController')->middleware(
        'dataValidation:auth.mfa_login,lume_pack.foundation'
    )->group(function () {
        Route::withoutMiddleware('lpfauth:sanctum')->put('mfa', 'mfa');
    });

    // routes relative to mfa mangment for logged users
    Route::prefix('mfa')->controller('MfaController')->middleware(
        'dataValidation:auth.mfa,lume_pack.foundation'
    )->group(function () {
        Route::post('/', 'establish');
        Route::put('/', 'enable');
        Route::delete('/', 'disable');
    });

    // Route used to validate the email
    Route::prefix('user/email')->controller('UserController')->group(function () {
        Route::withoutMiddleware('lpfauth:sanctum')->get('{token}', 'validate');
    });

    // Route used to ask for a forgoten login
    Route::prefix('user/login')->controller('LoginController')->middleware(
        'dataValidation:auth.login,lume_pack.foundation'
    )->group(function () {
        Route::withoutMiddleware('lpfauth:sanctum')->post('/', 'forgot');
    });

    // Routes used for a forgoten password or to renew it
    Route::prefix('pwd')->controller('PasswordController')->group(function () {
        Route::withoutMiddleware('lpfauth:sanctum')->middleware(
            'dataValidation:auth.passwordForgot,lume_pack.foundation'
        )->post('forgot', 'forgot');
        Route::middleware(
            'dataValidation:auth.passwordRenew,lume_pack.foundation'
        )->post('renew', 'renew');
        Route::withoutMiddleware('lpfauth:sanctum')->middleware(
            'dataValidation:auth.passwordForgotRenew,lume_pack.foundation'
        )->post('{token}', 'mailRenew');
    });

    // //
    // //
    // // TO DELETE
    // //
    // //
    // Route::prefix('role')->controller('RoleController')->middleware(
    //     'dataValidation:auth.role,lume_pack.foundation'
    // )->withoutMiddleware('lpfauth:sanctum')->group(function () {
    //     Route::get('/', 'list');
    //     Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
    //     Route::post('/', 'add');
    //     Route::put('{uid}', 'edit');
    //     Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
    // });

    // //
    // //
    // // TO DELETE
    // //
    // //
    // Route::prefix('permission')->controller('PermissionController')->middleware(
    //     'dataValidation:auth.permission,lume_pack.foundation'
    // )->withoutMiddleware('lpfauth:sanctum')->group(function () {
    //     Route::get('/', 'list');
    //     Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
    //     Route::post('/', 'add');
    //     Route::put('{uid}', 'edit');
    //     Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
    // });
});

// Routes for logs
Route::prefix('log')->namespace('Log')->middleware(
    'lpfauth:sanctum'
)->group(function () {
    Route::controller('LogController')->middleware(
        'dataValidation:log.log,lume_pack.foundation'
    )->group(function () {
        Route::get('/', 'list');
        Route::post('/', 'add');
        Route::post('mass', 'massAdd');
    });
});



//
//
//
//
// TEST LOGS
//
//
//
//

// //
// //
// // TO DELETE
// //
// //
// Route::prefix('user')->controller('Auth\UserController')->middleware(
//     'dataValidation:auth.user,lume_pack.foundation'
// )->withoutMiddleware('lpfauth:sanctum')->group(function () {
//     Route::get('/', 'list');
//     Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
//     Route::post('/', 'add');
//     Route::put('{uid}', 'edit');
//     Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
// });

// //
// //
// // TO DELETE
// //
// //
// Route::prefix('taxonomy')->namespace('Dictionaries')->withoutMiddleware(
//     'lpfauth:sanctum'
// )->group(function () {
//     Route::controller('TaxonomyController')->middleware(
//         'dataValidation:dictionaries.taxonomy,lume_pack.foundation'
//     )->group(function () {
//         Route::get('/', 'list');
//         Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
//         Route::post('/', 'add');
//         Route::put('{uid}', 'edit');
//         Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
//     });

//     Route::prefix('value')->controller('TaxonomyValueController')->middleware(
//         'dataValidation:dictionaries.taxonomy_value,lume_pack.foundation'
//     )->group(function () {
//         Route::get('/', 'list');
//         Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
//         Route::post('/', 'add');
//         Route::put('{uid}', 'edit');
//         Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
//         Route::post('upload', 'upload');
//         Route::get('stream/{token}.{action}', 'stream')->where([
//             'token' => '[0-9A-z]+', 'action' => 'show|down'
//         ]);
//     });
// });






// //
// //
// // TO DELETE
// //
// //
// Route::prefix('storage')->namespace('Storage')->group(function () {
//     Route::prefix('media')->controller('MediaController')->middleware(
//         'dataValidation:storage.media,lume_pack.foundation'
//     )->group(function () {
//         Route::get('/', 'list');
//         Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
//         Route::post('/', 'add');
//         Route::put('{uid}', 'edit');
//         Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
//     });

//     Route::prefix('file')->controller('FileController')->middleware(
//         'dataValidation:storage.file,lume_pack.foundation'
//     )->group(function () {
//         Route::get('/', 'list');
//         Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
//         Route::post('/', 'add');
//         Route::put('{uid}', 'edit');
//         Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
//         Route::post('upload', 'upload');
//         Route::get('stream/{token}.{action}', 'stream')->where([
//             'token' => '[0-9A-z]+', 'action' => 'show|down'
//         ]);
//     });
// });

//
//
// TO DELETE
//
//
// Route::prefix('cront_task')->controller('CRONTaskController')->middleware(
//     'dataValidation:CRONTask,lume_pack.foundation'
// )->withoutMiddleware('lpfauth:sanctum')->group(function () {
//     Route::get('/', 'list');
//     Route::get('{uid}', 'show')->where([ 'uid' => '[0-9]+' ]);
//     Route::post('/', 'add');
//     Route::put('{uid}', 'edit');
//     Route::delete('{uid}', 'remove')->where([ 'uid' => '[0-9]+' ]);
// });

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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use LumePack\Foundation\Http\Controllers\BaseController;
use LumePack\Foundation\Mail\BaseMail;

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
     * Method called by the /api/user/login URL in POST.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function forgot(Request $request): JsonResponse
    {
        $user_model = config('crud.user_model');
        Mail::send(new BaseMail('foundation::emails.user.logins', [
            'logins' => $user_model::where(
                'email', $request->email
            )->get()->pluck('login')->toArray(),
            'subject' => trans('foundation::mail.subject_user_logins')
        ]));

        return $this->response->format();
    }
}

<?php
/**
 * UserTrait class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use LumePack\Foundation\Mail\BaseMail;

/**
 * UserTrait
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait UserTrait
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootUserTrait()
    {
        static::saving(function (User $model) {
            $model->pwd_token_created_at = (
                is_null($model->pwd_token)? null: new \DateTime()
            );

            if ($model->getOriginal('email') !== $model->email) {
                Mail::send(new BaseMail('foundation::emails.user.validate', [
                    'user' => $model,
                    'token' => base64_encode($model->login),
                    'subject' => trans('foundation::mail.subject_user_validate')
                ]));

                $model->email_verified_at = null;
            }

            if (Hash::needsRehash($model->password)) {
                $model->password = Hash::make($model->password);
            }
        });

        static::saved(function (User $model) {
            if (Request::has('password') && Hash::check(Request::get('password'), $model->password)) {
                Mail::send(new BaseMail('foundation::emails.user.password', [
                    'user' => $model,
                    'subject' => trans('foundation::mail.subject_user_password')
                ]));
            }

            if (is_null($model->password) && $model->pwd_token) {
                $model->pwd_token = User::tokenize();

                Mail::send(new BaseMail('foundation::emails.auth.password', [
                    'user' => $model,
                    'subject' => trans('foundation::mail.subject_auth_password')
                ]));

                $model->save();
            }
        });
    }
}

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
use LumePack\Foundation\Data\Models\Mailing\Sendmail;
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

            if ($model->getOriginal(
                'email_verified_at'
            ) !== $model->email_verified_at) {
                $model->email_token = null;
            }

            if (Hash::needsRehash($model->password)) {
                $model->password = Hash::make($model->password);
            }
        });

        static::saved(function (User $model) {
            if (
                Request::has('password') &&
                Hash::check(Request::get('password'), $model->password)
            ) {
                Mail::send(new BaseMail('foundation::emails.user.password', [
                    'user' => $model,
                    'subject' => trans('foundation::mail.subject_user_password')
                ]));
            }

            if (
                $model->getOriginal('pwd_token') !== $model->pwd_token &&
                !is_null($model->pwd_token)
            ) {
                Mail::send(new BaseMail('foundation::emails.auth.forgot', [
                    'subject' => trans('foundation::mail.subject_auth_forgot'),
                    'user' => $model
                ]));
            }

            if ($model->getOriginal('email') !== $model->email) {
                $model->email_token = User::emailTokenize();
                $model->email_verified_at = null;
                $model->saveQuietly();

                Mail::send(new BaseMail('foundation::emails.user.validate', [
                    'user' => $model,
                    'token' => $model->email_token,
                    'subject' => trans(
                        'foundation::mail.subject_user_validate'
                    )
                ]));
            }

            if (is_null($model->password) && $model->pwd_token) {
                $model->pwd_token = User::pwdTokenize();

                Mail::send(new BaseMail('foundation::emails.auth.password', [
                    'user' => $model,
                    'subject' => trans('foundation::mail.subject_auth_password')
                ]));

                $model->save();
            }
        });
    }
}

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

            if (is_null($model->email_verified_at)) {
                $model->email_token = User::emailTokenize();
            }

            if (!is_null($model->password) && Hash::needsRehash($model->password)) {
                $model->password = Hash::make($model->password);
            }
        });

        static::saved(function (User $model) {
            // Send email validation when a new mail token is generated
            if (
                $model->getOriginal('email_token') !== $model->email_token &&
                !is_null($model->email_token)
            ) {
                $model->email_verified_at = (
                    config('auth.is_mail_relocked')? null: $model->email_verified_at
                );
                $model->saveQuietly();

                Mail::send(new BaseMail('foundation::emails.user.validate', [
                    'user' => $model,
                    'token' => $model->email_token,
                    'subject' => trans(
                        'foundation::mail.subject_user_validate'
                    )
                ]));
            }

            // Send email validation success on email verification
            if (
                !is_null($model->email_verified_at) &&
                $model->email_verified_at->ne($model->getOriginal('email_verified_at'))
            ) {
                Mail::send(new BaseMail('foundation::emails.user.email', [
                    'user' => $model,
                    'subject' => trans(
                        'foundation::mail.subject_user_validates'
                    )
                ]));
            }

            // Send password creation link when password and pwd_token empty
            if (
                is_null($model->password) &&
                is_null($model->pwd_token) &&
                is_null($model->deleted_at) &&
                $model->is_active
            ) {
                $model->pwd_token = User::pwdTokenize();
                $model->saveQuietly();

                Mail::send(new BaseMail('foundation::emails.auth.password', [
                    'user' => $model,
                    'subject' => trans('foundation::mail.subject_auth_password')
                ]));
            }

            // Send forgot password when new pwd token is generated
            if (
                $model->getOriginal('pwd_token') !== $model->pwd_token &&
                !is_null($model->pwd_token)
            ) {
                Mail::send(new BaseMail('foundation::emails.auth.forgot', [
                    'subject' => trans('foundation::mail.subject_auth_forgot'),
                    'user' => $model
                ]));
            }

            // Send password creation success on password change
            if (
                Request::has('password') &&
                Hash::check(Request::get('password'), $model->password)
            ) {
                Mail::send(new BaseMail('foundation::emails.user.password', [
                    'user' => $model,
                    'subject' => trans('foundation::mail.subject_user_password')
                ]));
            }
        });
    }
}

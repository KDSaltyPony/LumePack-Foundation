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
            $old_email = $model->getOriginal('email');

            $model->pwd_token_created_at = (
                is_null($model->pwd_token)? null: new \DateTime()
            );

            if ($old_email !== $model->email) {
                Mail::send(new BaseMail('foundation:emails.user.validate', [
                    'user' => $model,
                    'token' => base64_encode($model->email)
                ]));

                $model->email_verified_at = null;
            }

            if (Hash::needsRehash($model->password)) {
                $model->password = Hash::make($model->password);
            }
        });

        static::saved(function (User $model) {
        //     // TODO: old password !== new password => send mail!
        //     // TODO: old email !== new email => send confirmation mail
        //     // TODO: password is null => send password change mail
        });

        // static::created(function (User $model) {
        // //     // TODO: send mail confirmation mail ( = null)! code the endpoint to pass  at now
        // });
    }
}

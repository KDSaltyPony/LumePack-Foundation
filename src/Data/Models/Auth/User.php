<?php
/**
 * User class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use LumePack\Foundation\Database\Factories\Auth\UserFactory;

/**
 * User
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class User extends Authenticatable
{
    use UserModel, HasApiTokens, HasFactory, Notifiable;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    protected $uid = 'User';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [ /*'entite', 'profils', 'avatar'*/ ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ /*'nom_complet'*/ ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'deleted_at',
        // 'pwd_token', 'pwd_token_created_at'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */

    /**
     * Set the user's email.
     *
     * @param string $value The email value
     *
     * @return void
     */
    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Set the user's password.
     *
     * @param string $value The password value
     *
     * @return void
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }
}

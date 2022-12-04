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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\BaseAuthModel;
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
class User extends BaseAuthModel
{
    use UserTrait, HasFactory, SoftDeletes;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    public $log_uid = 'User';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [ 'is_active' => true ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [ 'roles' ];

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
        'password', 'remember_token', 'pivot', 'deleted_at',
        'pwd_token', 'pwd_token_created_at', 'email_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'pwd_token_created_at' => 'datetime', 'email_verified_at' => 'datetime'
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
     * Get the User's Roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role')->without(
            'users'
        )->without('permissions');
    }

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
    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['password'] = is_null($value)? null: Hash::make($value);
    }

    /**
     * Create a token.
     *
     * @return string
     */
    public static function pwdTokenize(): string
    {
        do {
            $token = Str::random(32);
        } while (!is_null(User::firstWhere('pwd_token', $token)));

        return $token;
    }

    /**
     * Create a token.
     *
     * @return string
     */
    public static function emailTokenize(): string
    {
        do {
            $token = Str::random(32);
        } while (!is_null(User::firstWhere('email_token', $token)));

        return $token;
    }
}

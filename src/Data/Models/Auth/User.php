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

use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Laravel\Sanctum\HasApiTokens;

/**
 * User
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
// class Utilisateur extends LoggedModel implements AuthenticatableContract, AuthorizableContract, JWTSubject
// {
//     use Authenticatable, Authorizable, HasFactory;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    protected $uid = 'User';
    // "id" Serial,
    // "created_at" Timestamp with time zone NOT NULL,
    // "updated_at" Timestamp with time zone,
    // "deleted_at" Timestamp with time zone,
    // "is_active" Boolean DEFAULT TRUE NOT NULL,
    // "login" Character varying NOT NULL,
    // "password" Character varying NOT NULL,
    // "email" Character varying NOT NULL
    // $table->timestamp('email_verified_at')->nullable();
    // $table->rememberToken();
    // remember_token 100 chars nullable

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
    protected $appends = [ /*'nom_complet', 'couleur', 'nombre_essai'*/ ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        /*'motdepasse', 'supprime', 'dat_entite_id', 'dat_media_id', 'pivot',
        'mdp_jeton', 'mdp_jeton_date'*/
    ];

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
}

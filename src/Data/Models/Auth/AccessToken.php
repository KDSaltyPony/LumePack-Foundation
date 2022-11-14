<?php
/**
 * AccessToken class file
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

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * AccessToken
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class AccessToken extends SanctumPersonalAccessToken
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_access_tokens';

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    protected $uid = 'AccessToken';
    // Schema::create('user_access_tokens', function (Blueprint $table) {
    //     $table->id();
    //     $table->morphs('tokenable');
    //     $table->string('name');
    //     $table->string('token', 64)->unique();
    //     $table->text('abilities')->nullable();
    //     $table->timestamp('last_used_at')->nullable();
    //     $table->timestamp('expires_at')->nullable();
    //     $table->timestamps();
    // });

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

<?php
/**
 * Sendmail class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Mailing
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Mailing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\BaseModel;

/**
 * Sendmail
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Mailing
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Sendmail extends BaseModel
{
    use HasFactory;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    protected $log_uid = 'Sendmail';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'deleted_at' ];

    // /**
    //  * Create a new factory instance for the model.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Factories\Factory
    //  */
    // protected static function newFactory()
    // {
    //     return SendmailFactory::new();
    // }

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
     * Create a token.
     *
     * @return string
     */
    public static function tokenize(): string
    {
        do {
            $token = Str::random(32);
        } while (!is_null(Sendmail::firstWhere('token', $token)));

        return $token;
    }
}

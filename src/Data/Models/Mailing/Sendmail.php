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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\BaseModel;
use LumePack\Foundation\Mail\BaseMail;

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
     * Set the sendmail's content.
     *
     * @param string $value The email value
     *
     * @return void
     */
    public function setContentAttribute(string|array $value): void
    {
        if (is_array($value)) {
            $this->attributes['content'] = "__json:" . json_encode(
                Sendmail::netralizeContent($value)
            );
        } else {
            $this->attributes['content'] = e($value);
        }
    }

    /**
     * Get the sendmail's content.
     *
     * @param string $value The first name value
     *
     * @return string|array
     */
    public function getContentAttribute(string $value): string|array
    {
        if (Str::startsWith($value, '__json:')) {
            $value = Sendmail::retriveContent(
                json_decode(Str::after($value, '__json:'))
            );
        }

        return $value;
    }

    /**
     * Set the sendmail's content.
     *
     * @param string|null $value The email value
     *
     * @return void
     */
    public function setTokenAttribute(?string $value): void
    {
        $this->attributes['token'] = (
            is_null($value)? Sendmail::tokenize(): $value
        );
    }

    /**
     * Send an email if content = [ \
     * "template" => "...", \
     * "attributes" => [ ... ] \
     * ]
     *
     * @return bool
     */
    public function send(): bool
    {
        if (
            is_array($this->content) &&
            array_key_exists('template', $this->content) &&
            array_key_exists('attributes', $this->content)
        ) {
            Mail::send(new BaseMail(
                $this->content['template'],
                $this->content['attributes']
            ));
        }

        return !is_null($this->sent_at);
    }

    /**
     * Netralize a data structure to store it. \
     * Remove objects that don't contain ids. \
     * If the object has an id it will be store to be retrived has a model.
     *
     * @return array
     */
    public static function netralizeContent(array $value): array
    {
        foreach ($value as $key => $val) {
            if (is_object($val)) {
                if (!is_null($val->id)) {
                    $value[$key] = get_class($val) . ":{$val->id}";
                } else {
                    unset($value[$key]);
                }
            } elseif (is_array($val)) {
                $value[$key] = Sendmail::netralizeContent($val);
            }
        }

        return $value;
    }

    /**
     * Retrive a data structure that has been netralized. \
     * Retrive the models based in the string \Namespace\Model:id.
     *
     * @return array
     */
    public static function retriveContent(array $value): array
    {
        foreach ($value as $key => $val) {
            if (is_string($val) && class_exists(Str::before($val, ':'))) {
                $class = Str::before($val, ':');
                $id = Str::after($val, ':');

                $value[$key] = $class::firstWhere('id', $id);
            } elseif (is_array($val)) {
                $value[$key] = Sendmail::retriveContent($val);
            }
        }

        return $value;
    }

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

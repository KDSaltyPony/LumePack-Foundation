<?php
/**
 * Sendmail class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Log
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Log;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\BaseModel;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

/**
 * Sendmail
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Log
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class Log extends MongoModel
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = [ "created_at" ];

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'process', 'source', 'code', 'data', 'created_at'
    ];

    // /**
    //  * Create a new factory instance for the model.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Factories\Factory
    //  */
    // protected static function newFactory()
    // {
    //     return LogFactory::new();
    // }

    /**
     * Create a new cast class instance.
     *
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        // $this->id = Log::all()->count() + 1;

        if (!isset($attributes['process'])) {
            $attributes['process'] = config('logs.process');
        }

        if (!isset($attributes['source'])) {
            $attributes['source'] = \Transliterator::createFromRules(
                ':: Any-Latin;'
                . ':: NFD;'
                . ':: [:Nonspacing Mark:] Remove;'
                . ':: NFC;'
                . ':: Upper();'
                . '[:Separator:] > \'-\''
            )->transliterate(env('APP_NAME', 'K')) . '-API';
        }

        parent::__construct($attributes);
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
     * Set the log's data.
     *
     * @param Model|Request|Response|array $value The data value
     *
     * @return void
     */
    public function setDataAttribute(Model|Request|Response|array|string $value): void
    {
        if ($value instanceof Model) {
            $value = [
                'uid' => $value->log_uid,
                'table' => $value->getTable(),
                'model' => get_class($value),
                'original' => $value->getRawOriginal(),
                'attributes' => $value->getAttributes()
            ];
        }

        if ($value instanceof Request) {
            $guzzle_request = $value->toPsrRequest();

            if ($guzzle_request instanceof GuzzleRequest) {
                $value = [
                    'method' => $guzzle_request->getMethod(),
                    //   -requestTarget: null
                    'protocol' => "{$guzzle_request->getUri()->getScheme()}:{$guzzle_request->getProtocolVersion()}",
                    // 'user' => $value->getUri()->getUserInfo(),
                    'host' => $guzzle_request->getUri()->getHost(),
                    'port' => $guzzle_request->getUri()->getPort(),
                    'path' => $guzzle_request->getUri()->getPath(),
                    'query_string' => $guzzle_request->getUri()->getQuery(),
                    'anchor' => $guzzle_request->getUri()->getFragment(),
                    //   -uri: GuzzleHttp\Psr7\Uri^ {#3138
                    //     -composedComponents: null },
                    'headers' => $guzzle_request->getHeaders(),
                    'body' => $value->data()
                ];
            } else {
                // TODO
                $value = [ 'error' => 'Can\'t log that kind of request (yet?).' ];
            }
        }

        if ($value instanceof Response) {
            $guzzle_response = $value->toPsrResponse();

            if ($guzzle_response instanceof GuzzleResponse) {
                $value = [
                    'status' => $guzzle_response->getStatusCode(),
                    'reason' => $guzzle_response->getReasonPhrase(),
                    'headers' => $guzzle_response->getHeaders(),
                    'body' => (
                        is_null(json_decode($value->body(), true))?
                            e($value->body()):
                            json_decode($value->body(), true)
                    )
                ];
            } else {
                // TODO
                $value = [ 'error' => 'Can\'t log that kind of response (yet?).' ];
            }
        }

        $value['is_authenticated'] = !is_null(auth()->user());

        if ($value['is_authenticated']) {
            $value['user_id'] = auth()->user()->id;
        }

        $this->attributes['data'] = is_string($value)? json_decode($value): $value;
    }

    // /**
    //  * Get the log's data.
    //  *
    //  * @param string $value The data value
    //  *
    //  * @return mixed
    //  */
    // public function getDataAttribute(string $value): mixed
    // {
    //     $value = json_decode($value, true);

    //     // if (
    //     //     array_key_exists('model', $value) &&
    //     //     array_key_exists('original', $value) &&
    //     //     array_key_exists('attributes', $value)
    //     // ) {
    //     //     TODO: retirve model and relations as they where (find in relations logs ???)
    //     // }
    //     // TODO: retirve user ???

    //     return $value;
    // }
}

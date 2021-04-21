<?php
/**
 * ResponseService class file
 * 
 * PHP Version 7.2.19
 * 
 * @category Service
 * @package  LumePack\Foundation\Services
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Services;

// use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Request;
// use Illuminate\Support\Facades\Response;

/**
 * ResponseService
 * 
 * @category Service
 * @package  LumePack\Foundation\Services
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class ResponseService
{
    /**
     * Association HTTP code => details
     * 
     * @var array
     */
    private const STATUS_CODES = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted - no success guarantee',
        203 => 'Non-Authoritative Information - non-certified source',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        400 => 'Bad Request',
        401 => 'Unauthorized - need authentication',
        402 => 'Payment Required',
        403 => 'Forbidden - not the appropriate rights',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable - not in "Accept" Header',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable - wrong "Range" Header',
        417 => 'Expectation failed - couldn\'t meet the "Expect" requirements',
        421 => 'Bad mapping / Misdirected Request - no answer from service'
    ];

    /**
     * The HTTP code.
     * 
     * @var int
     */
    protected $status = 200;

    /**
     * The response body.
     * 
     * @var mixed
     */
    protected $body = null;

    /**
     * The paginator.
     * 
     * @var Paginator
     */
    protected $paginator = null;

    /**
     * The response metadata.
     * 
     * @var array
     */
    protected $metadata = [];

    /**
     * Set the response object.
     * 
     * @param mixed $body   The data the response should return
     * @param int   $status The HTTP code of the response
     * 
     * @return void
     */
    public function __construct($body, int $status = 200)
    {
        $this->status = $status;
        $this->metadata = [
            'success' => ($this->status >= 200 && $this->status < 300),
            'status'  => $this->status,
            'message' => self::STATUS_CODES[$this->status]
        ];

        if (is_object($body) && get_class($body) === Collection::class && config('custom.paginator.limit') !== 0) {
            $this->body = $body->forPage(
                config('custom.paginator.page'),
                config('custom.paginator.limit')
            );

            $this->paginator = new Paginator(
                $this->body,
                $body->count(),
                config('custom.paginator.limit'),
                config('custom.paginator.page'),
                [
                    'query' => [
                        'sort'  => config('custom.query.sort'),
                        'limit' => config('custom.paginator.limit'),
                        'filters' => config('custom.query.filters')
                    ]
                ]
            );

            $this->_paginatorMetadata();
        } else {
            $this->body = $body;
        }
    }

    /**
     * Add or edit metadata info.
     * 
     * @param int   $key   The key of the metadata
     * @param mixed $value The value of the metadata
     * 
     * @return void
     */
    public function setMetaData(string $key, $value): void
    {
        $this->metadata[$key] = $value;
    }

    /**
     * Serealize and return a response in regard of the Accept header.
     * 
     * @param array $headers Optional response headers.
     * 
     * @return mixed
     */
    public function format(array $headers = [])
    {
        $accept = Request::header('Accept');
        $response = (
            new ResponseService('Accept header required', 400)
        )->_JSON();

        if ($this->status === 401) {
            $headers['WWW-Authenticate'] = 'Bearer realm="Access to the API"';
        }

        switch ($accept) {
            case 'application/json':
                $response = $this->_JSON($headers);
                break;

            // case 'application/xml':
            //     # code...
            //     break;

            // case 'application/octet-stream':
            //     Content-Disposition: attachment; filename="MyFileName.ext"...
            //     break;
        }

        return $response;
    }

    /**
     * Serealize (auto in Lumen ^^, override?) and return a JSON response.
     * 
     * @param array $headers Optional response headers.
     * 
     * @return JsonResponse
     */
    private function _JSON(array $headers = []): JsonResponse
    {
        return response()->json(
            [ 'meta' => $this->metadata, 'data' => $this->body ],
            $this->status,
            array_merge($headers, [
                'Content-Type' => 'application/json'
            ])
        );
    }

    // /**
    //  * Serealize and return a XML response.
    //  *
    //  * @param array $headers Optional response headers.
    //  * 
    //  * @return 
    //  */
    // private function _XML(array $headers = []): XmlResponse???
    // {
    // }

    /**
     * Set paginator metadata.
     * 
     * @return void
     */
    private function _paginatorMetadata(): void
    {
        if (!is_null($this->paginator)) {
            $this->metadata['pagination'] = [
                'page'         => $this->paginator->currentPage(),
                'last'         => $this->paginator->lastPage(),
                'url'          => $this->paginator->url(
                    $this->paginator->currentPage()
                ),
                'first_url'    => $this->paginator->url(1),
                'last_url'     => $this->paginator->url(
                    $this->paginator->lastPage()
                ),
                'previous_url' => $this->paginator->previousPageUrl(),
                'next_url'     => $this->paginator->nextPageUrl(),
                'count_over'   => $this->paginator->total(),
                'count'        => $this->paginator->count(),
                'limit'        => $this->paginator->perPage()
            ];
        }
    }
}
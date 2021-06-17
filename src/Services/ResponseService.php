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
use Illuminate\Http\File as HttpFile;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
// use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * The HTTP code.
     *
     * @var int
     */
    protected $status = 200;

    /**
     * The HTTP headers.
     *
     * @var int
     */
    protected $headers = [];

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
            'success'   => ($this->status >= 200 && $this->status < 300),
            'status'    => $this->status,
            'message'   => trans("status.{$status}")
        ];
        $this->body = $body;

        if (is_object($body) && get_class($body) === Paginator::class) {
            $this->body = $body->items();
            $this->body = (
                $this->body instanceof Collection?
                    $this->body: Collection::make($this->body)
            );

            $this->paginator = $body;

            $this->_paginatorMetadata();
        }
    }

    /**
     * Add or edit metadata info.
     *
     * @param int   $key   The key of the metadata
     * @param mixed $value The value of the metadata
     *
     * @return ResponseService
     */
    public function setMetaData(string $key, $value): ResponseService
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Add or edit metadata info.
     *
     * @param int    $key   The name of the header
     * @param string $value The value of the header
     *
     * @return ResponseService
     */
    public function setHeader(string $key, string $value): ResponseService
    {
        $this->headers[$key] = $value;

        return $this;
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

        if (app()->environment('local')) {
            $this->headers['Access-Control-Allow-Origin'] = '*';
        } else {
            $this->headers['Access-Control-Allow-Origin'] = env(
                'ACCESS_CONTROL_ALLOW_ORIGIN', '*'
            );
        }

        $this->headers['Access-Control-Allow-Headers'] = 'X-Requested-With, X-CSRF-TOKEN, Origin, Content-Type, Accept, Authorization';
        $this->headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS';
        $this->headers['Vary'] = 'Origin';
        // TODO : Vary header, Origin, etc. https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin
        // $this->headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, OPTIONS';
        // $this->headers['Access-Control-Allow-Headers'] = 'Accept, Content-Type, Authorization';
        // Access-Control-Allow-Methods: POST, GET, OPTIONS
        // Access-Control-Allow-Headers: Content-Type
        // res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
        // res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

        $this->headers = array_merge($this->headers, $headers);

        switch ($accept) {
            case 'application/json':
                $response = $this->_JSON($this->headers);
                break;

            // case 'application/xml':
            //     # code...
            //     break;

            case 'application/octet-stream':
                $response = $this->_file($this->headers);
                // TODO upload Content-Disposition: attachment; filename="MyFileName.ext"...
                break;
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

    /**
     * Serealize (auto in Lumen ^^, override?) and return a JSON response.
     *
     * @param array $headers Optional response headers.
     *
     * @return BinaryFileResponse
     */
    private function _file(array $headers = []): BinaryFileResponse
    {
        // return response()->file($this->body,
        //     $this->status,
        //     array_merge($headers, [
        //         'Content-Type' => File::mimeType($this->body)
        //     ])
        // );
        return (new BinaryFileResponse($this->body, $this->status, [
            'Content-Type' => File::mimeType($this->body)
        ]));
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

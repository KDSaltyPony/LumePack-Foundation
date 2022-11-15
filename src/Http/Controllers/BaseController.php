<?php
/**
 * BaseController class file
 *
 * PHP Version 7.2.19
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Controllers;

use LumePack\Foundation\Services\ResponseService;
use LumePack\Foundation\Data\Repositories\CRUD;
use Laravel\Lumen\Routing\Controller as LaravelController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * BaseController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
abstract class BaseController extends LaravelController
{
    /**
     * The Controller response service.
     *
     * @var ResponseService $response
     */
    protected $response;

    /**
     * The Repository.
     *
     * @var CRUD $repo
     */
    protected $repo = null;

    /**
     * List the methods that are not limited.
     *
     * @var array $unlimited
     */
    protected $unlimited = [];

    /**
     * Set the repository based on the child.
     *
     * @param CRUD $repo The CRUD child
     *
     * @return void
     */
    public function __construct(?CRUD $repo = null)
    {
        if (is_null($repo)) {
            $repo = ns_search(get_class($this), 'repository', [
                'Http' => 'Data'
            ]);
            $repo = is_null($repo)? null: new $repo();
        }

        $this->repo = $repo;
    }

    /**
     * Method called by the /{root} URL in GET.
     *
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $this->setResponse($this->repo->all($this->isLimited()));

        return $this->response->format();
    }

    /**
     * Method called by the /{root}/{uid} URL in GET.
     *
     * @param int     $uid     The unique id of the desired Model
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function show(int $uid, Request $request): JsonResponse
    {
        $this->setResponse($this->repo->read($uid, $this->isLimited()));

        return $this->response->format();
    }

    /**
     * Method called by the /{root} URL in POST.
     *
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $this->setResponse(
            $this->repo->create($request->post(), $this->isLimited()), 201
        );

        return $this->response->format();
    }

    /**
     * Method called by the /{root} URL in POST.
     *
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function massAdd(Request $request): JsonResponse
    {
        $this->setResponse(
            $this->repo->massCreate($request->post(), $this->isLimited()), 201
        );

        return $this->response->format();
    }

    /**
     * Method called by the /{root}/{uid} URL in PUT
     *
     * @param int     $uid     The unique id of the Model we want to edit
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function edit(int $uid, Request $request): JsonResponse
    {
        $this->setResponse(
            $this->repo->update($request->post(), $uid, $this->isLimited())
        );

        return $this->response->format();
    }

    /**
     * Method called by the /{root} URL in PUT
     *
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function massEdit(Request $request): JsonResponse
    {
        $this->setResponse(
            $this->repo->massUpdate($request->post(), $this->isLimited())
        );

        return $this->response->format();
    }

    /**
     * Method called by the /{root}/{uid} URL in DELETE.
     *
     * @param int     $uid     The unique id of the poor Model we are going to delete T.T
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function remove(int $uid, Request $request): JsonResponse
    {
        $this->setResponse(
            $this->repo->delete($request->post(), $uid, $this->isLimited())
        );

        return $this->response->format();
    }

    /**
     * Method called by the /{root} URL in DELETE.
     *
     * @param Request $request The injected Request
     *
     * @return JsonResponse
     */
    public function massRemove(Request $request): JsonResponse
    {
        $this->setResponse(
            $this->repo->massDelete($request->post(), $this->isLimited())
        );

        return $this->response->format();
    }


    /**
     * Set the response.
     *
     * @param mixed $body   The data the response should return
     * @param int   $status The HTTP code of the response
     *
     * @return void
     */
    protected function setResponse($body, int $status = 200)
    {
        if (is_bool($body)) {
            $body = ($body)? $this->repo->getModel(): null;
        }

        $this->response = new ResponseService(
            $body,
            (is_null($body)? 404: $status)
        );

        if (is_null($body)) {
            switch (debug_backtrace()[1]['function']) {
                case 'add':
                    $this->response->setMetaData(
                        'details', "{$this->getModelName()} creation failed"
                    );
                    break;

                default:
                    $this->response->setMetaData(
                        'details', "{$this->getModelName()} not found"
                    );
            }
        }
    }

    /**
     * Get the data type
     *
     * @return string
     */
    protected function getModelName(): string
    {
        return (!is_null($this->repo))?
            ucfirst(strtolower($this->repo->getModelClassName())):
            'Targeted data'
        ;
    }

    /**
     * Check if the calling method limit the access to the resource
     * to the Authenticated user only.
     *
     * @return string
     */
    protected function isLimited(): bool
    {
        return !in_array(debug_backtrace()[1]['function'], $this->unlimited);
    }
}

<?php
/**
 * FileController class file
 *
 * PHP Version 7.2.19
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Controllers\Storage;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LumePack\Foundation\Data\Models\Storage\File as StorageFile;
use LumePack\Foundation\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * FileController
 *
 * @category Controller
 * @package  LumePack\Foundation\Http\Controllers\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class FileController extends BaseController
{
    /**
     * Method called by the /storage/file/upload URL in DELETE.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
	public function upload(Request $request): JsonResponse
	{
        // TODO: MD5 checksum ou erreur???
        $meta = $request->input('meta');

        $this->setResponse(trans('storage.up_progress'), 200);

        if (
            StorageFile::chunk(
                $meta['name'],
                $request->input('order'),
                $request->file('chunk')
            ) === intval($meta['length'])
        ) {
            $file = StorageFile::build(
                $meta['name'], $meta['length']
            );
            $file['media_uid'] = $file['media_uid'];

            $this->setResponse(
                $this->repo->create($file, $this->isLimited()), 201
            );

            // TODO: vérification des dimensions
            // if (condition) {
            //     $this->setResponse(
            //         $this->repo->create($file, $this->isLimited()), 400
            //     );
            // }
        }

        return $this->response->format();
	}

    /**
     * Method called by the /storage/file/upload URL in DELETE.
     *
     * @param int     $token   The file token
     * @param int     $action  The stream action (show|down)
     * @param Request $request The request
     *
     * @return BinaryFileResponse|JsonResponse
     */
	public function stream(
        string $token, string $action, Request $request
    ): BinaryFileResponse|JsonResponse
	{
        $file = StorageFile::firstWhere('token', 'LIKE', $token);
        $is_errored = false;

        if (array_key_exists('width', $request->all())) {
            $width = intval($request->all()['width']);
            $file->setWidth($width);

            if (
                (
                    !is_null($file->media->min_width) &&
                    $file->media->min_width > $width
                ) || (
                    !is_null($file->media->max_width) &&
                    $file->media->max_width < $width
                )
            ) {
                $is_errored = true;

                $this->setResponse(trans('storage.wrong_width'), 400);
            }
        }

        if (array_key_exists('height', $request->all())) {
            $height = intval($request->all()['height']);
            $file->setHeight($height);

            if (
                (
                    !is_null($file->media->min_height) &&
                    $file->media->min_height > $height
                ) || (
                    !is_null($file->media->max_height) &&
                    $file->media->max_height < $height
                )
            ) {
                $is_errored = true;

                $this->setResponse(trans('storage.wrong_height'), 400);
            }
        }

        if (array_key_exists('croped', $request->all())) {
            $file->setIsCroped(boolval($request->all()['croped']));
        }

        if (!$is_errored) {
            $this->setResponse($file->variation_absolute_path, 200);

            if ($action === 'down') {
                $this->response->setHeader(
                    'Content-Disposition',
                    "attachment; filename=\"{$file->name}.{$file->extension}\""
                );
                // header("Content-Transfer-Encoding: Binary");
                // header("Content-Length:".filesize($attachment_location));
            }
        }

        return $this->response->format();
	}
}

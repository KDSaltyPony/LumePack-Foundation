<?php
/**
 * File class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\BaseModel;
use LumePack\Foundation\Database\Factories\Storage\FileFactory;

/**
 * File
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Storage
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class File extends BaseModel
{
    use HasFactory, FileTrait, SoftDeletes;

    /**
     * The uid associated with the model log.
     *
     * @var string
     */
    public $log_uid = 'File';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [ /*'media'*/ ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'deleted_at', 'media_id' ];

    /**
     * The width of an image.
     *
     * @var int
     */
    protected $width = null;

    /**
     * The height of an image.
     *
     * @var int
     */
    protected $height = null;

    /**
     * Is an image croped on resize.
     *
     * @var bool
     */
    protected $is_croped = true;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return FileFactory::new();
    }

    /**
     * -------------------------------------------------------------------------
     * Relations
     * -------------------------------------------------------------------------
     */

    /**
     * Get the File's Media.
     *
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class)->without('files');
    }

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */

    /**
     * Get the absolute path.
     *
     * @return string
     */
    public function getOriginalAbsolutePathAttribute(): string
    {
        return Storage::disk(config('storage.disk'))->path(
            config('storage.dir') . "/{$this->token}"
        );
    }

    /**
     * Get the absolute path.
     *
     * @return string
     */
    public function getVariationAbsolutePathAttribute(): string
    {
        $path = $this->original_absolute_path;
        $mimetypes = [];
        $width = getimagesize($path)[0];
        $height = getimagesize($path)[1];

        // TODO: put that in validation shit
        if (extension_loaded('imagick')) {
            $mimetypes = array_filter(File::apacheMimeTypes(
                (new \Imagick())->queryFormats()
            ), function ($extension, $mimetype) {
                return Str::startsWith($mimetype, 'image');
            }, ARRAY_FILTER_USE_BOTH);
        } elseif (extension_loaded('gd')) {
            // foreach (gd_info() as $type => $is_supported) {
            //     if ($is_supported) {
            //         if (!Str::contains($type, 'Create')) {
            //             $mimetypes[] = Str::before($type, ' ');
            //         }
            //     }
            // }

            // $mimetypes = File::apacheMimeTypes($mimetypes);
        }

        if (in_array(
            FacadesFile::mimeType($path), array_keys($mimetypes)
        ) && ((
            !is_null($this->width) && $this->width > 0 &&
            $this->width !== $width
        ) || (
            !is_null($this->height) && $this->height > 0 &&
            $this->height !== $height
        ))) {
            $name = config('storage.dir') . "/{$this->token}-";
            $name .= (is_null($this->width)? $width: $this->width);
            $name .= '-';
            $name .= (is_null($this->height)? $height: $this->height);
            $name .= $this->is_croped? '-c': '-nc';
            $old_path = $path;
            $path = Storage::disk(config('storage.disk'))->path($name);

            if (!Storage::disk(config('storage.disk'))->exists($name)) {
                if (extension_loaded('imagick')) {
                    $imagick = new \Imagick($old_path);
                    $new_ratio = $this->width / $this->height;
                    $old_ratio = $width / $height;

                    if ($this->is_croped && $new_ratio !== $old_ratio) {
                        $width_ratio = $this->width / $width;
                        $height_ratio = $this->height / $height;
                        $x_crop = abs(
                            ($new_ratio > $old_ratio)?
                                0: (($this->width - ($width * $height_ratio)) / 2) / $height_ratio
                        );
                        $y_crop = abs(
                            ($new_ratio > $old_ratio)?
                                (($this->height - ($height * $width_ratio)) / 2) / $width_ratio: 0
                        );
                        $width = $width - ($x_crop * 2);
                        $height = $height - ($y_crop * 2);

                        $imagick->cropImage($width, $height, $x_crop, $y_crop);
                    }

                    $imagick->scaleImage($this->width, $this->height);
                    $imagick->writeImage($path);
                }  elseif (extension_loaded('gd')) {
                    // TODO: GD images
                    // IMG_AVIF imageavif | IMG_BMP imagebmp | IMG_GIF imagegif | IMG_JPG imagejpeg | IMG_PNG imagepng | IMG_WBMP imagewbmp | IMG_XPM | IMG_WEBP imagewebp
                }
            }
        }

        return $path;
    }

    /**
     * Set the image width.
     *
     * @param int $width The image width
     *
     * @return void
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * Set the image height.
     *
     * @param int $height The image height
     *
     * @return void
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * Set the image crop.
     *
     * @param int $is_croped The image crop
     *
     * @return void
     */
    public function setIsCroped(bool $is_croped): void
    {
        $this->is_croped = $is_croped;
    }

    /**
     * Remove all local files.
     *
     * @return void
     */
    public function remove(): void
    {
        $this->prune(0);

        Storage::disk(config('storage.disk'))->delete(
            $this->original_absolute_path
        );
    }

    /**
     * Remove all local files.
     *
     * @param int|null $variations_keped The number of variations to keep (default: config.storage.max_variations)
     *
     * @return int
     */
    public function prune(int $variations_keped = null): int
    {
        $deleted = 0;

        $variations_keped = (
            is_null($variations_keped)?
                config('storage.max_variations'): $variations_keped
        );
        $files = FacadesFile::glob(
            "{$this->original_absolute_path}-$"
        );

        usort($files, function ($file_a, $file_b) {
            return filemtime($file_b) - filemtime($file_a);
        });

        for ($i = $variations_keped; $i < count($files); $i++) {
            $deleted += FacadesFile::delete($files[$i])? 1: 0;
        }

        return $deleted;
    }

    /**
     * Get MimeTypes from Apache config.storage.mimetypes_src \
     * The file must be formated like follow: \
     * \# This is a comment \
     * mime/type1 ext1 \
     * mime/type-2 ext21 ext22 \
     * mime/type.3      ext31 ext32 ext33 \
     * The function differenciate between mimtypes and extensions wit the "/". \
     * Mimetypes filters contains "/" (exemples: image/jpeg, image/*, * /*)
     *
     * @param string|array|null $filters A list of wildcarded mimtypes or extensions separeted by commas or as array
     *
     * @return array
     */
    public static function apacheMimeTypes(string|array $filters = null): array
    {
        $file = config('storage.mimetypes_src');

        $mimetypes = [];
        // $keys = [];
        $content = explode("\n", file_get_contents($file));

        foreach ($content as $value) {
            if (
                isset($value[0]) && $value[0] !== '#' &&
                preg_match_all('#([^\s]+)#', $value, $out) &&
                isset($out[1]) && ($c = count($out[1])) > 1
            ) {
                $value = Str::replace(
                    ' ', ',', Str::replaceFirst(
                        ' ', '@', preg_replace('/(?:\s|\t)+/', ' ', $value)
                    )
                );
                $mimetypes[] = $value;
            }
        }

        if (!is_null($filters)) {
            if (is_array($filters)) {
                $filters = join(',', $filters);
            }

            $filters = strtr(strtolower($filters), [
                ' ' => '', '+' => '\\+', '/' => '\\/', ',' => '$|^',
                '*' => '(?:.)+?'
            ]);
            $filters = preg_replace(
                '/((?:^|\\|\\^)[A-z0-9]+\\/[A-z0-9]+)(\\$|$)/',
                '$1@.+$2', $filters
            );
            $filters = preg_replace(
                '/(^|\\|\\^)([A-z0-9]+)(?:\\$|$)/',
                '$1.+?(?:,|@)$2(?:,.+?$|$)', $filters
            );

            $mimetypes = preg_grep("/^{$filters}$/", $mimetypes);
        }

        foreach ($mimetypes as $key => $mimetype) {
            $mimetypes[Str::before($mimetype, '@')] = explode(
                ',', Str::after($mimetype, '@')
            );
            unset($mimetypes[$key]);
        }

        ksort($mimetypes, SORT_STRING);

        return $mimetypes;
    }

    /**
     * Add a blob to the tmp folder.
     *
     * @param string       $name  The file name
     * @param int          $order The chunk number
     * @param UploadedFile $file  The chunk
     *
     * @return int
     */
    public static function chunk(
        string $name, int $order, UploadedFile $file
    ): int
    {
        $sname = self::systemFriendly($name);
        $dir = config('storage.dir') . "/tmp/{$sname}/";

        $file->storeAs($dir, "chunk-{$order}.tmp", config('storage.disk'));

        return count(Storage::disk(config('storage.disk'))->allFiles($dir));
    }

    /**
     * Rebuild a file from blobs.
     *
     * @param string      $name   The file name
     * @param int         $length The number of chunk
     * @param string|null $token  The token
     *
     * @return array
     */
    public static function build(
        string $name, int $length, string $token = null
    ): array
    {
        $sname = self::systemFriendly($name);
        $dir = config('storage.dir') . "/tmp/{$sname}/";
        $file = config('storage.dir') . "/{$token}";

        if (is_null($token)) {
            do {
                $token = self::systemFriendly(uniqid());
            } while (!is_null(File::firstWhere('token', $token)));
        }

        for ($i = 1; $i <= $length; $i++) {
            $content = Storage::disk(config('storage.disk'))->get("{$dir}chunk-{$i}.tmp");
            // if ($i === 1) {
            //     Storage::disk(config('storage.disk'))->put($file, $content);
            // } else {
                // file_put_contents(Storage::disk(config('storage.disk'))->path($file), $content);
            Storage::disk(config('storage.disk'))->append($file, $content);
            // Storage::disk(config('storage.disk'))->append($file, $content);
            // }
        }

        Storage::disk(config('storage.disk'))->deleteDirectory($dir);

        return [
            'name' => $name,
            'token' => $token,
            'size' => Storage::disk(config('storage.disk'))->size($file)
        ];
    }

    /**
     * Rebuild a file from blobs.
     *
     * @param string $str the str to transform
     *
     * @return string
     */
    public static function systemFriendly(string $str): string
    {
        $str = base64_encode($str);
        $str = Str::after(strtr($str, '+/=', '000'), 'Temp');

        return $str;
    }
}

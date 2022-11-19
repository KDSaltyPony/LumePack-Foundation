<?php
/**
 * FileTrait class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models\Storage;

/**
 * FileTrait
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models\Auth
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait FileTrait
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootEssaiFichesModel()
    {
        static::deleting(function (File $model) {
            $model->remove();
        });
    }
}

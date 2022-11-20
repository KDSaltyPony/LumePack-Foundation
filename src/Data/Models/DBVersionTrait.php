<?php
/**
 * DBVersionTrait class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DBVersionTrait
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait DBVersionTrait
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootDBVersion()
    {
        // TODO: execute sql then log result. If error don't register version
        // static::saving(function (DBVersion $model) {
            // DB::enableQueryLog();

            // User::whereHas(
            //     'orders.payments',fn ($q) => $q->where('amount', '>', 400)
            // )->get();

            // return DB::getQueryLog();



            // DB::statement
            // DB::listen(function ($query) {
            // });

            // DB::listen(function ($query) {
            //     Log::info($query->sql);
            //     Log::info($query->bindings);
            // });
        // });
    }
}

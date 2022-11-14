<?php
/**
 * LaravelServiceProvider class file
 *
 * PHP Version 7.2.19
 *
 * @category Controller
 * @package  LumePack\Foundation\Providers
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Providers;

use LumePack\Foundation\Http\Middleware\DataCheck;
use LumePack\Foundation\Http\Middleware\DataValidate;
use LumePack\Foundation\Http\Middleware\QueryStringToConfig;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use LumePack\Foundation\Data\Models\Auth\AccessToken;

/**
 * LaravelServiceProvider
 *
 * @category Service
 * @package  LumePack\Foundation\Providers
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        DB::enableQueryLog();

        $this->mergeConfigFrom(
            realpath(__DIR__.'/../../config/crud.php'), 'crud'
        );
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../../config/paginator.php'), 'paginator'
        );
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../../config/query.php'), 'query'
        );

        app('router')->pushMiddlewareToGroup('api', QueryStringToConfig::class);
        app('router')->aliasMiddleware('dataValidation', DataValidate::class);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Sanctum::ignoreMigrations();
        Sanctum::usePersonalAccessTokenModel(AccessToken::class);
    }
}

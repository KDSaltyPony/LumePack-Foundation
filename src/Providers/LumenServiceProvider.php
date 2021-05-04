<?php
/**
 * LumenServiceProvider class file
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

/**
 * LumenServiceProvider
 * 
 * @category Service
 * @package  LumePack\Foundation\Providers
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class LumenServiceProvider extends ServiceProvider
{
    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->configure('crud');
        $this->app->configure('paginator');
        $this->app->configure('query');

        $this->mergeConfigFrom(
            realpath(__DIR__.'/../../config/crud.php'), 'crud'
        );
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../../config/paginator.php'), 'paginator'
        );
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../../config/query.php'), 'query'
        );

        $this->app->middleware([ QueryStringToConfig::class ]);

        $this->app->routeMiddleware([
            'dataValidation' => DataValidate::class
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

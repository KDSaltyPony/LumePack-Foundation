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
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(DataCheck::class);
        $kernel->pushMiddleware(QueryStringToConfig::class);
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->configure('paginator');
        $this->app->configure('query');

        $path = realpath(__DIR__.'/../../config/paginator.php');
        $this->mergeConfigFrom($path, 'paginator');

        $path = realpath(__DIR__.'/../../config/query.php');
        $this->mergeConfigFrom($path, 'query');
    }
}

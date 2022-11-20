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

// use LumePack\Foundation\Http\Middleware\DataCheck;
use LumePack\Foundation\Http\Middleware\DataValidate;
use LumePack\Foundation\Http\Middleware\QueryStringToConfig;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use LumePack\Foundation\Data\Models\Auth\AccessToken;
use LumePack\Foundation\Http\Middleware\Authenticate;

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
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../../config/storage.php'), 'storage'
        );
        // $this->mergeConfigFrom(
        //     realpath(__DIR__.'/../../config/auth.php'), 'auth'
        // );
        // $this->mergeConfigFrom(
        //     realpath(__DIR__.'/../../config/sanctum.php'), 'sanctum'
        // );

        app('router')->pushMiddlewareToGroup('api', QueryStringToConfig::class);
        app('router')->aliasMiddleware('dataValidation', DataValidate::class);
        app('router')->aliasMiddleware('lpfauth', Authenticate::class);

        $this->loadMigrationsFrom(
            realpath(__DIR__.'/../../database/migrations')
        );

        $this->loadTranslationsFrom(
            realpath(__DIR__.'/../../resources/lang/'), 'foundation'
        );

        $this->loadViewsFrom(
            realpath(__DIR__.'/../../resources/views/'), 'foundation'
        );

        // $this->publishes([
        //     __DIR__.'/../lang' => $this->app->langPath('vendor/courier'),
        // ]);
        // $this->loadRoutesFrom(
        //     realpath(__DIR__.'/../../routes/api.php')
        // );
        Route::middleware('api')->namespace(
            'LumePack\\Foundation\\Http\\Controllers'
        )->prefix('api')->group(
            realpath(__DIR__.'/../../routes/api.php')
        );

        Sanctum::ignoreMigrations();
        Sanctum::usePersonalAccessTokenModel(AccessToken::class);
    }
}

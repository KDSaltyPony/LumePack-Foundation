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

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use LumePack\Foundation\Listeners;

/**
 * LaravelServiceProvider
 *
 * @category Service
 * @package  LumePack\Foundation\Providers
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSending::class => [ Listeners\MessageSendingListener::class ],
        MessageSent::class => [ Listeners\MessageSentListener::class ],
        RequestSending::class => [ Listeners\RequestSendingListner::class ],
        ResponseReceived::class => [ Listeners\RequestSentListner::class ],
    ];
// protected $listen = [
//     'Illuminate\Http\Client\Events\ConnectionFailed' => [
//         'App\Listeners\LogConnectionFailed',
//     ],
// ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}

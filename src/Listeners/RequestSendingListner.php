<?php

namespace LumePack\Foundation\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Queue\InteractsWithQueue;
use LumePack\Foundation\Data\Models\Log\Log;

class RequestSendingListner
{
    // /**
    //  * SendmailRepository
    //  *
    //  * @var SendmailRepository
    //  */
    // private $repo;

    // /**
    //  * Create the event listener.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->repo = new Log();
    // }

    /**
     * Handle the event.
     *
     * @param MessageSending $event
     *
     * @return void
     */
    public function handle(RequestSending $event)
    {
        $keyed = spl_object_id($event->request->toPsrRequest());
        $log = new Log();
        $log->code = "REQUEST-{$keyed}-SENDING";
        $log->data = $event->request;
        $log->save();
    }
}

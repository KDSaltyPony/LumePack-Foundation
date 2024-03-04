<?php

namespace LumePack\Foundation\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Queue\InteractsWithQueue;
use LumePack\Foundation\Data\Models\Log\Log;

class RequestSentListner
{
    /**
     * Handle the event.
     *
     * @param MessageSending $event
     *
     * @return void
     */
    public function handle(ResponseReceived $event)
    {
        if (config('is_logged')) {
            $keyed = spl_object_id($event->request->toPsrRequest());
            $log = new Log();
            $log->code = "REQUEST-{$keyed}-SENT";
            $log->data = $event->response;
            $log->save();
        }
    }
}

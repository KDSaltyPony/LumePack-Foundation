<?php

namespace LumePack\Foundation\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use LumePack\Foundation\Data\Repositories\Mailing\SendmailRepository;

class LogSentMessageListener
{
    /**
     * SendmailRepository
     *
     * @var SendmailRepository
     */
    private $repo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repo = new SendmailRepository();
    }

    /**
     * Handle the event.
     *
     * @param MessageSent $event
     *
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $this->repo->updateWhereSentAt([
            'is_success' => true
        ], $event->message->getHeaders()->get(
            'date'
        )->getDateTime());
    }

    /**
     * Handle a job failure.
     *
     * @param MessageSending $event
     * @param \Throwable     $exception
     *
     * @return void
     */
    public function failed(MessageSending $event, $exception)
    {
        $this->repo->updateWhereSentAt([
            'is_success' => false
        ], $event->message->getHeaders()->get(
            'date'
        )->getDateTime());
    }
}

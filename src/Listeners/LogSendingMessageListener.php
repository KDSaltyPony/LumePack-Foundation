<?php

namespace LumePack\Foundation\Listeners;

use App\Data\Repositories\Utilities\MailRepository;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use LumePack\Foundation\Data\Repositories\Mailing\SendmailRepository;

class LogSendingMessageListener
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
     * @param MessageSending $event
     *
     * @return void
     */
    public function handle(MessageSending $event)
    {
        // dd('event');
        $this->repo->create([
            'from'    => implode(', ', array_keys($event->message->getFrom())),
            'to'      => implode(', ', array_keys($event->message->getTo())),
            'subject' => $event->message->getSubject(),
            'content' => $event->message->getBody(),
            'sent_at' => $event->message->getHeaders()->get(
                'date'
            )->getDateTime()
        ]);
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

<?php

namespace LumePack\Foundation\Listeners;

use App\Data\Repositories\Utilities\MailRepository;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use LumePack\Foundation\Data\Models\Mailing\Sendmail;
use LumePack\Foundation\Data\Repositories\Mailing\SendmailRepository;

class MessageSendingListener
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
        $token = $event->message->getHeaders()->getHeaderBody(
            'x-metadata-sendmail-token'
        );

        if (!is_null($token)) {
            if (Sendmail::firstWhere('token', $token)) {
                $this->repo->updateWhereToken(
                    [
                        'sent_at' => new \DateTime(),
                        'content' => $event->message->getBody()->getBody()
                    ], $token
                );
            } else {
                $this->repo->create([
                    'from'       => $event->message->getFrom(),
                    'to'         => $event->message->getTo(),
                    'subject'    => $event->message->getSubject(),
                    'content'    => $event->message->getBody()->getBody(),
                    'sent_at'    => new \DateTime(),
                    'token'      => $token,
                    'is_success' => null
                ]);
            }
        }
    }

    /**
     * Handle a job failure.
     *
     * @param MessageSending $event
     * @param \Throwable     $exception
     *
     * @return void
     */
    public function failed(MessageSending $event, \Throwable $exception)
    {
        $token = $event->message->getHeaders()->getHeaderBody(
            'x-metadata-sendmail-token'
        );

        if (!is_null($token)) {
            $this->repo->updateWhereToken([ 'is_success' => false ], $token);
        }
    }
}

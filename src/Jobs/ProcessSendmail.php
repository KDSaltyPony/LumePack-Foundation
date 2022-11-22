<?php

namespace LumePack\Foundation\Jobs;

use App\Services\AudioProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LumePack\Foundation\Data\Models\Mailing\Sendmail;

class ProcessSendmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The podcast instance.
     *
     * @var Sendmail
     */
    public $mail;

    /**
     * Create a new job instance.
     *
     * @param  Sendmail  $podcast
     * @return void
     */
    public function __construct(Sendmail $mail)
    {
        $this->mail = $mail;
    }

    // /**
    //  * Execute the job.
    //  *
    //  * @param  SendmailProcessor  $processor
    //  * @return void
    //  */
    // public function handle(SendmailProcessor $processor)
    // {
    //     // Process uploaded podcast...
    // }
}

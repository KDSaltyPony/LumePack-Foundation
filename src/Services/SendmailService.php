<?php
/**
 * SendmailService class file
 *
 * PHP Version 7.2.19
 *
 * @category Service
 * @package  LumePack\Foundation\Services
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Services;

use Illuminate\Database\Eloquent\Collection;
use LumePack\Foundation\Data\Models\Mailing\Sendmail;

/**
 * SendmailService
 *
 * @category Service
 * @package  LumePack\Foundation\Services
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
abstract class SendmailService
{
    /**
     * The emails we need to send
     *
     * @var Collection
     */
    protected $emails = null;

    /**
     * Retrive the mails to send.
     */
    public function __construct()
    {
        $this->emails = Sendmail::whereNull('sent_at')->orderBy(
            'created_at', 'DESC'
        )->limit(config('sendmail.max_sent'))->get();
    }

    /**
     * Get the emails.
     *
     * @return Collection
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    /**
     * Send the emails.
     *
     * @return int
     */
    public function send(): int
    {
        $succeed = 0;

        foreach ($this->emails as $email) {
            $succeed += $email->send()? 1: 0;
        }

        return $succeed;
    }
}

<?php

namespace LumePack\Foundation\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use LumePack\Foundation\Data\Models\Mailing\Sendmail;

class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The Utilisateur instances.
     *
     * @var Utilisateur
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $template, array $attributes = [])
    {
        $this->user = auth()->user();
        $this->template = $template;
        $this->attributes = $attributes;
        $this->sendmail_token = null;

        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }

        $this->sendmail_token = (
            is_null($this->sendmail_token)?
                Sendmail::tokenize(): $this->sendmail_token
        );

        if (!key_exists('subject', $attributes)) {
            $this->subject = trans(
                'foundation::mail.subject_default',
                [ 'app' => env('APP_NAME', 'Laravel') ]
            );
        }

        if (!key_exists('from_address', $attributes)) {
            $this->from_address = env('MAIL_FROM_ADDRESS');
        }

        if (!key_exists('from_name', $attributes)) {
            $this->from_name = env('MAIL_FROM_NAME');
        }

        $this->from_address = new Address(
            $this->from_address, $this->from_name
        );

        if (!key_exists('to_addresses', $attributes)) {
            $this->to_addresses = [ (
                is_null($this->user)? [
                    'email' => env('MAIL_FROM_ADDRESS'),
                    'name' => env('MAIL_FROM_NAME')
                ]: [
                    'email' => $this->user->email,
                    'name' => $this->user->login
                ]
            ) ];
        }

        foreach ($this->to_addresses as $key => $to_address) {
            $this->to_addresses[$key] = new Address(
                $to_address['email'], $to_address['name']
            );
        }

        if (!key_exists('user', $attributes)) {
            $attributes['user'] = $this->user;
        }
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: $this->from_address,
            to: $this->to_addresses,
            subject: $this->subject,
            metadata: [
                'sendmail-token' => $this->sendmail_token
            ]
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content()
    {
        return new Content(
            view: $this->template,
            with: $this->attributes
        );
    }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array
    //  */
    // public function attachments()
    // {
    //     return [];
    // }

    // /**
    //  * Build the message.
    //  *
    //  * @return $this
    //  */
    // public function build()
    // {
    //     return $this->subject(
    //         'Vos identifiants'
    //     )->view('mails.users.forgot_pseudo');
    // }
}

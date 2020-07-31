<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAccount extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    // public function __construct($message)
    // {
    //     $this->message = $message;
    // }
    public function __construct($username, $subject)
    {
        $this->username = $username;
        $this->subject = $subject;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->markdown('emails.newaccount');
    }
}

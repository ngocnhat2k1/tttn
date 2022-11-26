<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $title = "";
    private $user = "";
    private $header = "";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $title, $header)
    {
        $this->user = $user;
        $this->title = $title;
        $this->header = $header;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->header)
            ->markdown('emails.resetpassword', [
                "title" => $this->title,
                "user" => $this->user,
            ]);
    }
}

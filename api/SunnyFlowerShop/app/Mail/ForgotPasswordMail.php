<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user = "";
    private $resetCode = "";
    private $title = "";
    private $header = "";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $resetCode, $title, $header)
    {
        $this->user = $user;
        $this->resetCode = $resetCode;
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
            ->markdown('emails.forgotpassword', [
                "user" => $this->user,
                "resetCode" => $this->resetCode,
                "title" => $this->title,
            ]);
    }
}

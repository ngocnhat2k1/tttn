<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDeliveredState extends Mailable
{
    use Queueable, SerializesModels;

    private $title = "";
    private $user = "";
    private $idDelivery = "";
    private $price = "";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $user, $idDelivery, $price)
    {
        $this->title = $title;
        $this->user = $user;
        $this->idDelivery = $idDelivery;
        $this->price = number_format($price, 0, ',', '.') . "đ";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("$this->title")
            ->markdown('emails.orderdeliveredstate', [
                "title" => $this->title,
                "user" => $this->user,
                "idDelivery" => $this->idDelivery,
                "price" => $this->price,
            ]);
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlaceOrderMailNotifyAdmin extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $subject_mail = "";
    private $title = "";
    private $text = "";
    private $idDelivery = "";
    private $priceOrder = "";
    private $listProducts = [];

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject_mail, $title, $text, $idDelivery, $priceOrder, $listProducts)
    {
        $this->subject_mail = $subject_mail;
        $this->title = $title;
        $this->text = $text;
        $this->idDelivery = $idDelivery;        ;
        $this->priceOrder = number_format($priceOrder, 0, ',', '.') . "đ";
        $this->listProducts = $listProducts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject_mail)
            ->markdown('emails.placeordernotifyadmin', [
                "title" => $this->title,
                "idDelivery" => $this->idDelivery,
                "text" => $this->text,
                "priceOrder" => $this->priceOrder,
                "listProducts" => $this->listProducts
            ]);
    }
}

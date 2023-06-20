<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderFormEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $orderFormMessage;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderFormMessage)
    {
        $this->orderFormMessage = $orderFormMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->orderFormMessage->title)
                    ->view('emails.order_form_email')
                    ->with([
                        'content' => $this->orderFormMessage->message,
                        'address_info' => $this->orderFormMessage->address,
                        'com_name' => $this->orderFormMessage->user->com_name,
                        'name' => $this->orderFormMessage->user->name,
                        'order_form_id' => $this->orderFormMessage->order_form_id
                    ]);
    }

    public function render()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->orderFormMessage->title)
                    ->view('emails.order_form_email')
                    ->with([
                      'content' => $this->orderFormMessage->message,
                      'address_info' => $this->orderFormMessage->address,
                      'com_name' => $this->orderFormMessage->user->com_name,
                      'name' => $this->orderFormMessage->user->name,
                      'order_form_id' => $this->orderFormMessage->order_form_id
                  ]);
    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMessageEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $orderMessage;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderMessage)
    {
        $this->orderMessage = $orderMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->orderMessage->title)
                    ->view('emails.order_message_email')
                    ->with([
                        'content' => $this->orderMessage->message,
                        'deadline' => $this->orderMessage->deadline,
                        'address_info' => $this->orderMessage->address,
                        'com_name' => $this->orderMessage->user->com_name,
                        'name' => $this->orderMessage->user->name,
                    ]);
    }

    public function render()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->orderMessage->title)
                    ->view('emails.order_message_email')
                    ->with([
                      'content' => $this->orderMessage->message,
                      'deadline' => $this->orderMessage->deadline,
                      'address_info' => $this->orderMessage->address,
                      'com_name' => $this->orderMessage->user->com_name,
                      'name' => $this->orderMessage->user->name,
                  ]);
    }
}
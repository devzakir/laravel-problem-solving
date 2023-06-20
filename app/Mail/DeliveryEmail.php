<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $mail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail)
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->mail->title)
                    ->view('emails.delivery_email')
                    ->with([
                        'content' => $this->mail->message,
                        'address_info' => $this->mail->address,
                        'com_name' => $this->mail->user->com_name,
                        'name' => $this->mail->user->name,
                    ]);
    }

    public function render()
    {
      return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->mail->title)
                    ->view('emails.delivery_email')
                    ->with([
                        'content' => $this->mail->message,
                        'address_info' => $this->mail->address,
                        'com_name' => $this->mail->user->com_name,
                        'name' => $this->mail->user->name,
                    ]);
    }
}
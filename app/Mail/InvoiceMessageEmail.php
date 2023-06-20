<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMessageEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $invoiceMessage;
    private $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoiceMessage, $url)
    {
        $this->invoiceMessage = $invoiceMessage;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->invoiceMessage->title)
                    ->view('emails.invoice_message_email')
                    ->with([
                        'content' => $this->invoiceMessage->message,
                        'address_info' => $this->invoiceMessage->address,
                        'com_name' => $this->invoiceMessage->user->com_name,
                        'name' => $this->invoiceMessage->user->name,
                        'url' => $this->url
                    ]);
    }

    public function render()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->invoiceMessage->title)
                    ->view('emails.invoice_message_email')
                    ->with([
                      'content' => $this->invoiceMessage->message,
                      'address_info' => $this->invoiceMessage->address,
                      'com_name' => $this->invoiceMessage->user->com_name,
                      'name' => $this->invoiceMessage->user->name,
                      'url' => $this->url
                  ]);
    }
}
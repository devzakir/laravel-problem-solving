<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $quote;
    private $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($quote, $url)
    {
        $this->quote = $quote;
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
                    ->subject($this->quote->subject_title)
                    ->view('emails.quote_email')
                    ->with([
                        'com_name' => $this->quote->recipienter->name,
                        'tanto_name' => $this->quote->recipienter->tanto_name,
                        'url' => $this->url
                    ]);
    }

    public function render()
    {
        return $this->from(['address' => 'noreply@conecle.jp', 'name' => 'CONECLE'])
                    ->subject($this->quote->subject_title)
                    ->view('emails.quote_email')
                    ->with([
                      'com_name' => $this->quote->recipienter->name,
                      'tanto_name' => $this->quote->recipienter->tanto_name,
                      'url' => $this->url
                  ]);
    }
}
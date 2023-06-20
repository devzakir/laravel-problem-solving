<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification\Notification;

class InviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $invite;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' => 'admin@test.com', 'name' => 'PLUS DOUGA事務所'])
                    ->subject('PLUS DOUGAクリエイター招待')
                    ->view('emails.invite')
                    ->with([
                        'url' => 'https://ukerundesu.com/creator/register?'.'email='.$this->invite->email.'&token='.$this->invite->token
                    ]);
    }

    public function render()
    {
        return $this->from(['address' => 'admin@test.com', 'name' => 'PLUS DOUGA事務所'])
                    ->subject('PLUS DOUGAクリエイター招待')
                    ->view('emails.invite')
                    ->with([
                        'url' => 'https://ukerundesu.com/creator/register?'.'email='.$this->invite->email.'&token='.$this->invite->token
                    ]);
    }
}
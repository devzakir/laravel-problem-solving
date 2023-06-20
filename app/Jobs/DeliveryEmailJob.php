<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeliveryEmail;
use App\User;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderMessage;
use App\Models\MailHistory;

class DeliveryEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $mail;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MailHistory $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Mail::to($this->mail->user->email)->queue(new DeliveryEmail($this->mail));
    }
}
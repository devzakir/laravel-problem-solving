<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMessageEmail;
use App\User;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderMessage;

class OrderMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $orderMessage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OrderMessage $orderMessage)
    {
        $this->orderMessage = $orderMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Mail::to($this->orderMessage->user->email)->queue(new OrderMessageEmail($this->orderMessage));
    }
}
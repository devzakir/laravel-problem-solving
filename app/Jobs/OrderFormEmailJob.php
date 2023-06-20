<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderFormEmail;
use App\User;
use App\Models\OrderFormMessage;

class OrderFormEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $orderFormMessage;
    private $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OrderFormMessage $orderFormMessage, User $user)
    {
        $this->orderFormMessage = $orderFormMessage;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->queue(new OrderFormEmail($this->orderFormMessage));
    }
}
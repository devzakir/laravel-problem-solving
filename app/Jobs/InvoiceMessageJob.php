<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMessageEmail;
use App\User;
use App\Models\InvoiceMessage;

class InvoiceMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $invoiceMessage;
    private $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(InvoiceMessage $invoiceMessage, $url)
    {
        $this->invoiceMessage = $invoiceMessage;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Mail::to($this->invoiceMessage->user->email)->queue(new InvoiceMessageEmail($this->invoiceMessage, $this->url));
    }
}
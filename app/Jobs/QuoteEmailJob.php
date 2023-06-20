<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuoteEmail;
use App\User;
use App\Models\Quote;

class QuoteEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $quote;
    private $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Quote $quote, $url)
    {
        $this->quote = $quote;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Mail::to($this->quote->email)->queue(new QuoteEmail($this->quote, $this->url));
    }
}
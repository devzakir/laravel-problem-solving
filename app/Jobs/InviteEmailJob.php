<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invite;
use App\Mail\InviteEmail;
use Illuminate\Support\Facades\Mail;

class InviteEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invite;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        Invite $invite
    )
    {
        $this->invite = $invite;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->invite->email)->queue(new InviteEmail($this->invite));
    }
}
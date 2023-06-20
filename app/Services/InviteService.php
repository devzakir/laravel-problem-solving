<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\FileUpload;
use App\Enums\Role;
use App\Jobs\InviteEmailJob;
use Illuminate\Support\Facades\Storage;
use App\Models\Invite;
use Illuminate\Support\Str;

/**
 * Class InviteService
 * @package App\Services
 */
class InviteService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    
    public function getInvites() {
        return Invite::orderByDesc('created_at')->get();
    }

    public function deleteInvites($email) {
        Invite::where('email', $email)->delete();
        return true;
    }

    public function createInvite($email) {
        Invite::where('email', $email)->delete();

        $invite = Invite::create([
            'email' => $email,
            'token' => Str::random(32),
        ]);

        // email send
        try {
            InviteEmailJob::dispatch($invite);
        } catch (Exception $e) {
            \Log::error($e);
            return false;
        }

        return true;
    }
}

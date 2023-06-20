<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\User;
use App\Models\News;
use App\Models\Refund;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\FileUpload;
use App\Enums\Role;
use Illuminate\Support\Facades\Storage;

/**
 * Class RefundService
 * @package App\Services
 */
class RefundService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    public function getAllRefunds() {
        return Refund::orderByDesc('created_at')
                     ->with('user')
                     ->get();
    }

    public function createRefund($attributes) {
        $refund = Refund::create($attributes);

        News::create([
            'content' => $refund->price.'￥の入金がありました。',
            'user_id' => $refund->user_id,
            'type' => 4
        ]);

        return true;
    }
}

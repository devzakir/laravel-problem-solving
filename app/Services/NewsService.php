<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\User;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\FileUpload;
use App\Enums\Role;
use Illuminate\Support\Facades\Storage;

/**
 * Class NewsService
 * @package App\Services
 */
class NewsService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    public function getNews() {
        return News::where('user_id', Auth::guard('api')->user()->id)
                   ->orWhereNull('user_id')
                   ->orderByDesc('created_at')
                   ->get();
    }

    public function getAllNews() {
        return News::orderByDesc('created_at')
                   ->with('user')
                   ->get();
    }

    public function createNews($attributes) {
        News::create($attributes);

        return true;
    }

    public function deleteNews($attributes) {
        $id = Arr::get($attributes, 'id');

        News::where('id', $id)->delete();

        return true;
    }
}

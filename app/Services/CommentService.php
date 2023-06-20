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
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use App\Models\CommentReply;

/**
 * Class CreatorService
 * @package App\Services
 */
class CommentService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    
    public function reply($user, $attributes) {
		return CommentReply::create([
			'comment_id' => Arr::get($attributes, 'comment_id'),
			'creator_id' => $user->id,
			'content' => Arr::get($attributes, 'reply')
		]);
	}
	
	public function postComment($attributes) {
		$attributes['buyer_id'] = Auth::guard('api')->user()->id;

		return Comment::create($attributes);
	}
}

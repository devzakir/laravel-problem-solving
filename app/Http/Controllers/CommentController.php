<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommentService;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
	/**
     * @var CommonService
     */
    protected $service;

    /**
     * CreatorController constructor
     * 
     */
    public function __construct(
		CommentService $service
    )
    {
        $this->service = $service;
    }

    public function replyComment(Request $request) {
		$reply = $this->service->reply(Auth::guard('api')->user(), $request->all());
		return response()->json(['reply' => $reply]);
	}
	
	public function postComment(Request $request) {
		$newComment = $this->service->postComment($request->all());
		return response()->json(['new_comment' => $newComment]);
	}
}

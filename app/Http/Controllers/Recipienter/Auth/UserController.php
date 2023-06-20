<?php

namespace App\Http\Controllers\Recipienter\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipienter;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        $recipienter_id = Auth::guard('recipienter')->user()->id;
        $recipienter = Recipienter::where('id', $recipienter_id)->with('childUsers', 'parent')->first();
        return response()->json($recipienter);
    }
}

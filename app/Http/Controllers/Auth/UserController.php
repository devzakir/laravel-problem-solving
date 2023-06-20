<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $query = \App\User::where('id', $request->user()->id);

        if ($request->user()->role_id == Role::BUYER) {
            $user = $query->with('buyer')->first();
            return response()->json($user);
        } else {
            $user = $query->with('creator', 'creatorBank')->first();
            return response()->json($user);
        }   
    }
}

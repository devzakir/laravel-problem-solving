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
use App\Models\Buyer\Buyer;
use App\Models\Creator\Creator;
use App\Models\Creator\Movie;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

/**
 * Class BuyerService
 * @package App\Services
 */
class BuyerService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    public function updateName($name, $user) {
        User::where('id', $user->id)->update([
            'name' => $name
        ]);

        Buyer::where('user_id', $user->id)->update([
            'nickname' => $name
        ]);

        return User::where('id', $user->id)->with('buyer')->first();
    }

    public function updateEmail($email, $user) {
        User::where('id', $user->id)->update([
            'email' => $email
        ]);

        return User::where('id', $user->id)->with('buyer')->first();
    }

    public function updatePassword($password, $user) {
        User::where('id', $user->id)->update([
            'password' => Hash::make($password)
        ]);

        return User::where('id', $user->id)->with('buyer')->first();
    }

    public function updateTelephone($telephone, $user) {
        Buyer::where('user_id', $user->id)->update([
            'telephone' => $telephone
        ]);

        return User::where('id', $user->id)->with('buyer')->first();
    }

    public function changeAvatar($avatar, $user) {
        $path = $this->uploadFile($avatar, 'buyer/images');

        User::where('id', $user->id)->update([
            'avatar' => $path
        ]);

        return User::where('id', $user->id)->with('buyer')->first();
    }

    public function removeAvatar($user) {
        User::where('id', $user->id)->update([
            'avatar' => null
        ]);

        return User::where('id', $user->id)->with('buyer')->first();
    }

    public function getPaymentHistory() {
        return Payment::where('buyer_id', Auth::guard('api')->user()->id)->with('movie')->get();
    }

    public function getCreators() {
        return User::whereIn('id', function($query) {
            $query->select('creator_id')
                  ->from(with(new Payment)->getTable())
                  ->where('buyer_id', Auth::guard('api')->user()->id)
                  ->where('type', 'buy');
        })->with('creator', 'payments', 'comments', 'movies')->get();
    }

    public function getUsers() {
        return User::where('role_id', 1)->with('buyer', 'paymentsForBuyer')->get();
    }

    public function getUserDetailInfo($id) {
        return User::where('id', $id)->with('buyer', 'paymentsForBuyer')->first();
    }

    public function getCreatorsForAdmin() {
        return User::where('role_id', 2)->with('creator', 'paymentsForCreator', 'creatorBank', 'refunds')->get();
    }

    public function getCreatorDetailInfo($id) {
        return User::where('id', $id)->with('creator', 'paymentsForCreator', 'creatorBank')->first();
    }

    public function updateUserFee($attributes) {
        $buyer = Buyer::where('user_id', $attributes['user_id'])->first();
        $buyer->servic_fee = $attributes['fee'];
        $buyer->pay_fee = $attributes['fee2'];
        $buyer->save();

        return User::where('id', $attributes['user_id'])->with('buyer')->first();
    }

    public function blockCreator($attributes) {
        $creator_id = $attributes['creator_id'];
        $state = $attributes['state'];

        Creator::where('id', $creator_id)->update([
            'is_blocked' => ($state + 1) % 2
        ]);

        $creator = Creator::where('id', $creator_id)->first();

        Movie::where('creator_id', $creator->user_id)->update([
            'is_blocked' => ($state + 1) % 2
        ]);
    }
}
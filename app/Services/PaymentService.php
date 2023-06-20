<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\FileUpload;
use App\Enums\Role;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Models\Creator\Movie;
use App\Models\News;
use App\Models\Creator\Creator;
use App\Models\Buyer\Buyer;

/**
 * Class CreatorService
 * @package App\Services
 */
class PaymentService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    
    public function getCreatorPaymentHistory($user) {
		return Payment::where('creator_id', $user->id)->orderByDesc('paid_at')->with('buyer', 'movie')->get();
    }

    public function donate($attributes) {
		//決済
		$user = Auth::user();

		$pay_jp_secret = 'sk_live_4c7f95fc1a6ae8ffc796dbbd1d0e4948494fd9a0bd984e84d14d9da6';
		$pay_jp_key = 'pk_live_a79fec4be6ae938a36ba5ce2';

		\Payjp\Payjp::setApiKey($pay_jp_secret);

		$cus_id = "";

		if (is_null($attributes['token'])) {
			$cus_id = $user->cus_id;
		} else {
			$customer = \Payjp\Customer::create(array(
				"card" => $attributes['token']
			));

			$cus_id = $customer->id;
			User::where('id', $user->id)->update([
				'cus_id' => $cus_id
			]);
		}
		$price = Arr::get($attributes, 'price');
		$creator = Creator::where('user_id', Arr::get($attributes, 'creator_id'))->first();
		$buyer = Buyer::where('user_id', $user->id)->first();

		$temp = intval($price * ( 1 + $buyer->servic_fee / 100 + $buyer->pay_fee / 100 ) + 40);
		try {
            $res = \Payjp\Charge::create(
                [
                    "customer" => $cus_id,
                    "amount" => $temp,//支払額。テストなのでランダムにしとく。
					"currency" => 'jpy',
					"tenant" => $creator->tenant_id
                ]
            );

            if (isset($res['error'])) {
                throw new Exception();
            }

        } catch (Exception $e) {
            // カードが拒否された場合
            $res['error']['message'];
            exit;
        }

		// payment 保存
		$attributes['buyer_id'] = $user->id;
		$attributes['paid_at'] = Carbon::now();

		Payment::create($attributes);

		return User::where('id', $user->id)->with('buyer')->first();
    }

    public function getAllPayments() {
		$months = Payment::select(\DB::raw('SUBSTRING(created_at, 1, 7) as payment_date'))
						->distinct('payment_date')
						->orderBy('payment_date', 'DESC')
						->get();

		if (sizeof($months) === 0) {
			$months[] = [
				'payment_date' => Carbon::now()->format("Y-m")
			];
		}

		return [ 
			Payment::orderByDesc('created_at')->with('buyer', 'creator', 'movie')->get(),
			User::where('role_id', \App\Enums\Role::CREATOR)->get(),
			$months
		];
		  
    }

    public function nyukinAction($attributes) {
		$payment_id = Arr::get($attributes, 'id');

		Payment::where('id', $payment_id)->update([
			'refund' => 2
		]);

		$payment = Payment::where('id', $payment_id)->first();

		$content = '';
		if ($payment->type == 'buy') {
			$content = '動画「'.$payment->movie->title.'」の購入に関する入金がありました。('.$payment->price.'￥)';
		} else {
			$content = '動画「'.$payment->movie->title.'」に関する投げ銭がありました。('.$payment->price.'￥)';
		}

		// お知らせ
		News::create([
			'content' => $content,
			'user_id' => $payment->creator->id
		]);

		return true;
	}
	
	public function searchPayments($attributes) {
		$query = Payment::where('id', '>', '0');

		$movie_title = Arr::get($attributes, 'movie_title');

		if (!is_null($movie_title)) {
			$query->whereIn('movie_id', function($query) use ($movie_title) {
				$query->select('id')
					  ->from(with(new Movie)->getTable())
					  ->where('title', 'like', '%'.$movie_title.'%');
			});
		}

		$creator_id = Arr::get($attributes, 'creator_id');

		if ($creator_id != 0) {
			$query->where('creator_id', $creator_id);
		}

		$month = Arr::get($attributes, 'month');

		if ($month != 0) {
			$query->where('created_at', 'like', $month.'%');
		}

		return $query->orderByDesc('created_at')->with('buyer', 'creator', 'movie')->get();
	}
}

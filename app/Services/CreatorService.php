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
use App\Models\Creator\Creator;
use App\Models\Creator\CreatorBank;

/**
 * Class CreatorService
 * @package App\Services
 */
class CreatorService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    
    public function update($user, $attributes) {
      // update
      $user = User::where('id', $user->id)->first();
      $user->name = Arr::get($attributes, 'name');
      if (Arr::get($attributes, 'isChanged')) {
        $user->avatar = Arr::get($attributes, 'avatar_url');
      }
      $user->save();

      $profileAttributes = Arr::get($attributes, 'profile');
      $profileAttributes['nickname'] = Arr::get($attributes, 'name');
      Creator::where('user_id', $user->id)->update($profileAttributes);

      $bankAttributes = Arr::get($attributes, 'bank');

      $creatorBank = CreatorBank::where('creator_id', $user->id)->first();
      if (!is_null($creatorBank)) {
        CreatorBank::where('creator_id', $user->id)->update($bankAttributes);
      } else {
        $bankAttributes['creator_id'] = $user->id;
        CreatorBank::create($bankAttributes);
      }

      $pay_jp_secret = 'sk_live_4c7f95fc1a6ae8ffc796dbbd1d0e4948494fd9a0bd984e84d14d9da6';
      $pay_jp_key = 'pk_live_a79fec4be6ae938a36ba5ce2';
      
      \Payjp\Payjp::setApiKey($pay_jp_secret);

      $tenant = \Payjp\Tenant::create(
        array(
          "name" => $user->id."-".$user->name,
          "platform_fee_rate" => "5.00",
          "minimum_transfer_amount" => 1000,
          "bank_account_holder_name" => $bankAttributes['account_name'],
          "bank_code" => $bankAttributes['bank_code'],
          "bank_branch_code" => $bankAttributes['branch_code'],
          "bank_account_type" => "普通",
          "bank_account_number" => $bankAttributes['account_number'],
        )
      );

      Creator::where('user_id', $user->id)->update([
        'tenant_id' => $tenant->id
      ]);

      return User::where('id', $user->id)->with('creator', 'creatorBank')->first();
    }

    public function changeAvatar($avatar = null) {
      $path = $this->uploadFile($avatar, 'creator/images');
      return $path;
    }
    
}

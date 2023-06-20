<?php

namespace App\Observers;

use App\Enums\Role;
use App\Models\Creator\Creator;
use App\Models\Buyer\Buyer;
use App\Models\Creator\CreatorBank;
use App\User;
use Illuminate\Support\Str;

class UserObserver
{
    public function created(User $user)
    {
        self::createProfileFrom($user);
    }

    private function createProfileFrom($user)
    {
        if ($user->role_id) {
            switch ($user->role_id) {
                case Role::BUYER:
                    if (!$user->buyer) {
                        Buyer::create([
                            'user_id' => $user->id,
                            'nickname' => $user->name,
                        ]);
                    }
                    break;
                case Role::CREATOR:
                    if (!$user->creator) {
                        Creator::create([
                            'user_id' => $user->id,
                            'nickname' => $user->name,
                        ]);

                        User::where('id', $user->id)->update([
                            'hash' => Str::uuid()
                        ]);

                        CreatorBank::create([
                            'creator_id' => $user->id
                        ]);
                    }
                    break;
            }
        }
    }
}

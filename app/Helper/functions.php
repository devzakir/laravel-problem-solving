<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

function get_path_image_by_role($role)
{
    switch ($role) {
        case \App\Enums\Role::BUYER:
            return 'buyer/images';
        case \App\Enums\Role::CREATOR:
            return 'creator/images';
    }
}

function generate_rand_password($length = 8)
{
    return str_random($length);
}
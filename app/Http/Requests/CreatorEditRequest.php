<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatorEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                    => ['required', 'string', 'max:255'],
            'profile'                 => ['required', 'array'],
            'profile.birthday'        => ['required'],
            'profile.address'         => ['required', 'string', 'max:255'],
            'profile.introduction'    => ['required', 'string', 'max:2000'],
            'bank'                    => ['required', 'array'],
            'bank.bank_name'          => ['required', 'string', 'max:255'],
            'bank.branch_name'        => ['required', 'string', 'max:255'],
            'bank.bank_type'          => ['required', 'string', 'max:255'],
            'bank.account_number'     => ['required', 'string', 'max:255'],
            'bank.account_name'       => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'name'                    => '名前',
            'profile.birthday'        => '生年月日',
            'profile.address'         => 'アドレス',
            'profile.introduction'    => '概要',
            'bank.bank_name'          => '金融機関名',
            'bank.branch_name'        => '支店名',
            'bank.bank_type'          => '預金種別',
            'bank.account_number'     => '口座番号',
            'bank.account_name'       => '口座名義',
        ];
    }

}

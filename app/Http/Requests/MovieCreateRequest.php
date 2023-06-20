<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieCreateRequest extends FormRequest
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
            'movie'                    => ['required', 'array'],
            'movie.title'              => ['required', 'string', 'max:255'],
            'movie.thumbnail'          => ['required', 'string', 'max:255'],
            'movie.description'        => ['required', 'string'],
            'movie.price'              => ['required', 'integer', 'max:99999999'],
            'movie.url'                => ['required', 'string'],
            'category'                 => ['required', 'integer'],
            'tags'                     => ['array'],
        ];
    }

    public function attributes()
    {
        return [
            'movie'                    => '動画',
            'movie.title'              => 'タイトル',
            'movie.thumbnail'          => 'サムネイル',
            'movie.description'        => '説明文',
            'movie.price'              => '価格',
            'movie.url'                => '動画URL',
            'category'                 => 'カテゴリー',
            'tags'                     => 'タグ',
        ];
    }

}

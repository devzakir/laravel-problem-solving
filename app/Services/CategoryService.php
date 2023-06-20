<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\User;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\FileUpload;
use App\Enums\Role;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

/**
 * Class CategoryService
 * @package App\Services
 */
class CategoryService
{
    public function getAllCategories() {
        return Category::get();
    }

    public function removeCategory($attributes) {
        $category_id = $attributes['category_id'];

        Category::where('id', $category_id)->delete();
    }

    public function createNewCategory($attributes) {
        $category_name = $attributes['category_name'];
        $newCategory = Category::create([
            'name' => $category_name
        ]);

        return $newCategory;
    }
}

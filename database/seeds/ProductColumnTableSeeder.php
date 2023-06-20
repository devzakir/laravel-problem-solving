<?php

use Illuminate\Database\Seeder;

use App\Enums\ProductColumnType;

class ProductColumnTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('product_columns')->truncate();

        DB::table('product_columns')->insert([
            [ 
                'name' => '商品名',
                'type' => ProductColumnType::Text,
                'order' => 1,
                'slug' => 'name',
                'picked' => 1,
                'needed' => 1
            ],
            [ 
                'name' => '発注単価',
                'type' => ProductColumnType::Number,
                'order' => 2,
                'slug' => 'price',
                'picked' => 1,
                'needed' => 1
            ],
            [ 
                'name' => '数量',
                'type' => ProductColumnType::Number,
                'order' => 3,
                'slug' => 'amount',
                'picked' => 1,
                'needed' => 1
            ],
            [ 
                'name' => '発注金額',
                'type' => ProductColumnType::Number,
                'order' => 4,
                'slug' => 'total_price',
                'picked' => 1,
                'needed' => 1
            ],
            [ 
                'name' => '備考',
                'type' => ProductColumnType::Text,
                'order' => 5,
                'slug' => 'remark',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => 'メーカー',
                'type' => ProductColumnType::Text,
                'order' => 6,
                'slug' => 'maker',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => 'カラー',
                'type' => ProductColumnType::Text,
                'order' => 7,
                'slug' => 'color',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => 'サイズ',
                'type' => ProductColumnType::Text,
                'order' => 8,
                'slug' => 'size',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => '単位',
                'type' => ProductColumnType::Text,
                'order' => 9,
                'slug' => 'unit',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => '画像',
                'type' => ProductColumnType::Image,
                'order' => 10,
                'slug' => 'image',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => 'JAN',
                'type' => ProductColumnType::Text,
                'order' => 11,
                'slug' => 'jan',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => '品番',
                'type' => ProductColumnType::Number,
                'order' => 12,
                'slug' => 'order',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => '自由項目1',
                'type' => ProductColumnType::Text,
                'order' => 13,
                'slug' => 'free_space1',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => '自由項目2',
                'type' => ProductColumnType::Text,
                'order' => 14,
                'slug' => 'free_space2',
                'picked' => 0,
                'needed' => 0
            ],
            [ 
                'name' => '自由項目3',
                'type' => ProductColumnType::Text,
                'order' => 15,
                'slug' => 'free_space3',
                'picked' => 0,
                'needed' => 0
            ],
        ]);
    }
}

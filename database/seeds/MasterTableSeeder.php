<?php

use Illuminate\Database\Seeder;

class MasterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('masters')->truncate();

        DB::table('masters')->insert([
            [ 
                'platform_fee' => 1.5,
                'min_price' => 1000
            ],
        ]);
    }
}

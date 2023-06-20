<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('admins')->truncate();

        DB::table('admins')->insert([
            [ 
                'email' => 'adminukeru9z@ukerundesu.com',
                'password' => bcrypt('vhefyxg7kui5') 
            ],
        ]);
    }
}

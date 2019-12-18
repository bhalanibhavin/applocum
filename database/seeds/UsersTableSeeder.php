<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'applocum admin',
            'email' => 'applocumadmin@yopmail.com',
            'password' => bcrypt('Password@123'),
        ]);
    }
}

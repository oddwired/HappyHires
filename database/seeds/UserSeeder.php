<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            "name" => "Shem Kiptoo",
            "email"=> "shemkemboi@gmail.com",
            "phone"=> "0716383258",
            "national_id"=> "12345678",
            "password"=> bcrypt("1234test")
        ]);
    }
}

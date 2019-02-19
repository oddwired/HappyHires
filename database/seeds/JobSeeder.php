<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("jobs")->insert([
            "user_id" => 1,
            "name" => "Web App",
            "description"=> "Create a web app",
            "location"=> "Nakuru",
            "price"=> 10000.00
        ]);
    }
}

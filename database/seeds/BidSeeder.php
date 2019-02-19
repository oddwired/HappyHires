<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("bids")->insert([
            "user_id" => 1,
            "job_id"=> 1,
            "price"=> 10000.00
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    	DB::table(config('constants.COUNTRY_MASTER_TABLE'))->insert([
    			'v_country_name' => 'India',
    			'i_created_id' => 999,
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'v_ip' => '::1',
    	]);
    }
}

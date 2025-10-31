<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    	DB::table(config('constants.LOOKUP_MASTER_TABLE'))->insert( [ [
    			'v_module_name' => config('constants.BANK_LOOKUP'),
    			'v_value' => 'HDFC Bank',
    			'i_sequence' => 1,
    			'i_created_id' => 999,
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'v_ip' => '::1',
    	],[
    			'v_module_name' => config('constants.RECRUITMENT_SOURCE_LOOKUP'),
    			'v_value' => 'Employee',
    			'i_sequence' => 2,
    			'i_created_id' => 999,
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'v_ip' => '::1',
    	] ] );
    }
}

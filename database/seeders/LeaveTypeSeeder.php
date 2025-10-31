<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    	DB::table(config('constants.LEAVE_TYPE_MASTER_TABLE'))->insert( [ [
    			'i_id' => config('constants.PAID_LEAVE_TYPE_ID') ,
    			'v_leave_type_name' => 'Paid Leave',
    			'v_leave_sort_code' => 'PL',
    			'i_created_id' => 999,
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'v_ip' => '::1',
    	],[
    			'i_id' => config('constants.EARNED_LEAVE_TYPE_ID'),
    			'v_leave_type_name' => 'Earned Leave',
    			'v_leave_sort_code' => 'EL',
    			'i_created_id' => 999,
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'v_ip' => '::1',
    	],[
    			'i_id' => config('constants.CARRY_FORWARD_LEAVE_TYPE_ID'),
    			'v_leave_type_name' => 'Carry Forward Leave',
    			'v_leave_sort_code' => 'CFL',
    			'i_created_id' => 999,
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'v_ip' => '::1',
    	] ] );
    }
}

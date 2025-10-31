<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class SalaryComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    	DB::table(config('constants.SALARY_COMPONENTS_MASTER_TABLE'))->insert([
    			'v_component_name' => 'PF',
    			'e_salary_components_type' => config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'),
    			'i_created_id' => 999,
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'v_ip' => '::1',
    	]);
    }
}

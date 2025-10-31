<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    	DB::table(config('constants.LOGIN_MASTER_TABLE'))->insert([
    			'v_name' => 'Admin',
    			'v_email' => 'info@thewildtigers.com',
    			'v_role' => config('constants.ROLE_ADMIN'),
    			'v_mobile' => '9999999999',
    			'v_password' => password_hash("admin", PASSWORD_DEFAULT),
    			'i_created_id' => '1',
    			'dt_created_at' => date('Y-m-d H:i:s'),
    			'i_updated_id' => '1',
    			'v_ip' => '::1',
    	]);
    }
}

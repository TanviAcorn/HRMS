<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuspendInfoEmployeeMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
    		$table->tinyInteger('t_is_suspended')->after('e_employment_status')->default('0');
    		$table->date('dt_suspended_start_date')->after('t_is_suspended')->nullable();
    		$table->date('dt_suspended_end_date')->after('dt_suspended_start_date')->nullable();
    		$table->integer('i_last_suspend_record_id')->after('dt_suspended_end_date')->nullable();
    		
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

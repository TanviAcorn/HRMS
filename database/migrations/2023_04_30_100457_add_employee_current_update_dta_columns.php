<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeCurrentUpdateDtaColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config("constants.EMPLOYEE_MASTER_TABLE"), function (Blueprint $table) {
    		$table->date('dt_last_update_designation')->after('dt_notice_period_end_date')->nullable();
    		$table->date('dt_last_update_team')->after('dt_last_update_designation')->nullable();
    		$table->date('dt_last_update_week_off')->after('dt_last_update_team')->nullable();
    		$table->date('dt_last_update_shift')->after('dt_last_update_week_off')->nullable();
    		$table->integer('i_probation_update_id')->after('dt_last_update_shift')->nullable();
    		$table->integer('i_login_id')->after('i_id');
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

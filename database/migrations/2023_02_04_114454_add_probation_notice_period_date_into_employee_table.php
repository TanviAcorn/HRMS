<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProbationNoticePeriodDateIntoEmployeeTable extends Migration
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
    		$table->date('dt_probation_end_date')->after('e_in_probation')->nullable();
    		$table->date('dt_notice_period_start_date')->after('dt_probation_end_date')->nullable();
    		$table->date('dt_notice_period_end_date')->after('dt_notice_period_start_date')->nullable();
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

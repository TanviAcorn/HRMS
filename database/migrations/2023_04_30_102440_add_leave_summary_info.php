<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveSummaryInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config("constants.EMPLOYEE_ATTENDANCE_TABLE"), function (Blueprint $table) {
    		$table->time('t_original_break_start_time')->after('t_total_break_time')->nullable();
    		$table->time('t_break_start_time')->after('t_total_original_break_time')->nullable();
    		$table->time('t_original_break_end_time')->after('t_original_break_start_time')->nullable();
    		$table->time('t_break_end_time')->after('t_break_start_time')->nullable();
    		$table->time('t_total_original_break_time')->after('t_original_break_end_time')->nullable();
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

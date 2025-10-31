<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.ATTENDANCE_SUMMARY_TABLE'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_employee_id');
        	$table->date('dt_month');
        	$table->double('d_present_count')->nullable();
        	$table->double('d_absent_count')->nullable();
        	$table->double('d_half_leave_count')->nullable();
        	$table->double('d_suspend_count')->nullable();
        	$table->double('d_adjustment_count')->nullable();
        	$table->double('d_paid_leave_count')->nullable();
        	$table->double('d_unpaid_leave_count')->nullable();
        	$table->tinyInteger('t_is_active')->default('1');
        	$table->tinyInteger('t_is_deleted')->default('0');
        	$table->integer('i_created_id');
        	$table->dateTime('dt_created_at');
        	$table->integer('i_updated_id')->nullable();
        	$table->dateTime('dt_updated_at')->nullable();
        	$table->integer('i_deleted_id')->nullable();
        	$table->dateTime('dt_deleted_at')->nullable();
        	$table->ipAddress('v_ip')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_summary');
    }
}

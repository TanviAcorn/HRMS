<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_employee_id');
        	$table->date('dt_date');
        	$table->time('t_start_time')->nullable();
        	$table->time('t_end_time')->nullable();
        	$table->time('t_total_working_time')->nullable();
        	$table->time('t_original_start_time')->nullable();
        	$table->time('t_original_end_time')->nullable();
        	$table->time('t_total_break_time')->nullable();
        	$table->tinyInteger('t_manually_change')->default('0');
        	$table->tinyInteger('t_is_half_leave')->default('0');
        	$table->enum('e_status', [config('constants.ABSENT_STATUS'),config('constants.PRESENT_STATUS'),config('constants.HALF_LEAVE_STATUS'),config('constants.WEEKOFF_STATUS'),config('constants.HOLIDAY_STATUS'),config('constants.SUSPEND_STATUS'),config('constants.ADJUSTMENT_STATUS')])->default(config('constants.ABSENT_STATUS'));
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
        Schema::dropIfExists('employee_attendance');
    }
}

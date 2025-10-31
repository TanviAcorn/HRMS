<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( config('constants.UPLOAD_DAILY_ATTENDANCE_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
        	$table->date('dt_attendance_date');
        	$table->longText('v_pay_code');
        	$table->longText('v_department');
        	$table->longText('v_name');
        	$table->longText('v_start')->nullable();
        	$table->longText('v_shift');
        	$table->longText('v_in')->nullable();
        	$table->longText('v_out')->nullable();
        	$table->longText('v_hour_worked')->nullable();
        	$table->longText('v_status')->nullable();
        	$table->longText('v_early_arrival')->nullable();
        	$table->longText('v_shift_late')->nullable();
        	$table->longText('v_shift_early')->nullable();
        	$table->longText('v_ot')->nullable();
        	$table->longText('v_ot_amount')->nullable();
        	$table->longText('v_over_stay')->nullable();
        	$table->longText('v_manual')->nullable();
        	$table->longText('v_in_location')->nullable();
        	$table->longText('v_out_location')->nullable();
        	$table->tinyInteger('t_is_manage')->default('0');
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
        Schema::dropIfExists(config('constants.UPLOAD_DAILY_ATTENDANCE_TABLE'));
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiAttendanceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( config('constants.API_ATTENDANCE_DETAILS') , function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_user_id');
            $table->date('dt_process_date');
            $table->time('t_start_time')->nullable();
            $table->time('t_end_time')->nullable();
            $table->text('v_first_half')->nullable();
            $table->text('v_second_half')->nullable();
            $table->text('t_lunch_start_time')->nullable();
            $table->text('t_lunch_end_time')->nullable();
            $table->text('t_lunch_time')->nullable();
            $table->text('t_work_time')->nullable();
            $table->longText('v_api_response')->nullable();
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
        Schema::dropIfExists(config('constants.API_ATTENDANCE_DETAILS'));
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.SHIFT_HISTORY_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_shift_master_id');
            $table->time('v_monday_start_time');
            $table->time('v_monday_end_time');
            $table->time('v_monday_break_start_time')->nullable();
            $table->time('v_monday_break_end_time')->nullable();
            $table->time('v_tuesday_start_time');
            $table->time('v_tuesday_end_time');
            $table->time('v_tuesday_break_start_time')->nullable();
            $table->time('v_tuesday_break_end_time')->nullable();
            $table->time('v_wednesday_start_time');
            $table->time('v_wednesday_end_time');
            $table->time('v_wednesday_break_start_time')->nullable();
            $table->time('v_wednesday_break_end_time')->nullable();
            $table->time('v_thursday_start_time');
            $table->time('v_thursday_end_time');
            $table->time('v_thursday_break_start_time')->nullable();
            $table->time('v_thursday_break_end_time')->nullable();
            $table->time('v_friday_start_time');
            $table->time('v_friday_end_time');
            $table->time('v_friday_break_start_time')->nullable();
            $table->time('v_friday_break_end_time')->nullable();
            $table->time('v_saturday_start_time');
            $table->time('v_saturday_end_time');
            $table->time('v_saturday_break_start_time')->nullable();
            $table->time('v_saturday_break_end_time')->nullable();
            $table->time('v_sunday_start_time');
            $table->time('v_sunday_end_time');
            $table->time('v_sunday_break_start_time')->nullable();
            $table->time('v_sunday_break_end_time')->nullable();
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
        Schema::dropIfExists(config('constants.SHIFT_HISTORY_TABLE'));
    }
}

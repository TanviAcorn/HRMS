<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShiftMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create(config('constants.SHIFT_MASTER_TABLE'), function (Blueprint $table) {
    		$table->increments('i_id');
    		$table->longText('v_shift_name');
    		$table->longText('v_shift_code');
    		$table->enum('e_shift_type',[config('constants.MORNING_SHIFT'),config('constants.NIGHT_SHIFT')])->default(config('constants.MORNING_SHIFT'));
    		$table->enum('e_different_week_day_time',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->default(config('constants.SELECTION_NO'));
    		$table->enum('e_different_week_day_break_time',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->default(config('constants.SELECTION_NO'));
    		$table->longText('v_description')->nullable();
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
        //
    	Schema::dropIfExists(config('constants.SHIFT_MASTER_TABLE'));
    }
}

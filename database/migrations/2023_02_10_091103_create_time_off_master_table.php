<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeOffMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.TIME_OFF_MASTER_TABLE'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_employee_id');
        	$table->date('dt_time_off_date');
        	$table->enum('e_record_type' , [ config("constants.ADJUSTMENT_TIME_OFF") , config("constants.OFFICIAL_WORK_TIME_OFF") ] )->default(config("constants.OFFICIAL_WORK_TIME_OFF"));
        	$table->time('t_from_time');
        	$table->time('t_to_time');
        	$table->longText('v_remark')->nullable();
        	$table->enum('e_status',[config('constants.PENDING_STATUS'),config('constants.APPROVED_STATUS'),config('constants.REJECTED_STATUS'),config('constants.CANCELLED_STATUS')])->default( config('constants.PENDING_STATUS') );
        	$table->dateTime('dt_approved_at')->nullable();
    		$table->integer('i_approved_by_id')->nullable();
    		$table->longText('v_approve_reject_remark')->nullable();
    		$table->longText('v_year')->nullable();
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
        Schema::dropIfExists('time_off_master');
    }
}

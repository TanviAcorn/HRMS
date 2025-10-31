<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateEmployeeDesignationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMPLOYEE_DATA_UPDATE_REQUEST'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_employee_id');
        	$table->enum('e_record_type' , [ config('constants.DESIGNATION_LOOKUP')  , config('constants.TEAM_LOOKUP') ,  config('constants.SHIFT_RECORD_TYPE') , config('constants.TIME_OFF_RECORD_TYPE')  ] )->nullable();
        	$table->integer('i_update_request_id');
        	$table->date('dt_effective_date');
        	$table->tinyInteger('t_is_updated')->default('0');
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
        Schema::dropIfExists(config('constants.EMPLOYEE_DATA_UPDATE_REQUEST'));
    }
}

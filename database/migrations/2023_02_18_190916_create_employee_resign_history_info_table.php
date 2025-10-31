<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeResignHistoryInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMPLOYEE_RESIGN_HISTORY'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->enum('e_initiate_type' , [ config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') , config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ] )->default(config('constants.EMPLOYEE_INITIATE_EXIT_TYPE'));
        	$table->enum('e_employee_discuss' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->nullable();
        	$table->integer('i_termination_reason_id')->nullable();
        	$table->integer('i_resign_reason_id')->nullable();
        	$table->date('dt_termination_notice_date')->nullable();
        	$table->date('dt_employee_notice_date')->nullable();
        	$table->enum('e_last_working_day' , [ config('constants.NOTICE_PERIOD') , config('constants.OTHER') ])->nullable();
        	$table->enum('e_rehire_status' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->nullable();
        	$table->date('dt_last_working_date')->nullable();
        	$table->longText('v_remark')->nullable();
        	$table->longText('v_attachment')->nullable();
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
        Schema::dropIfExists('employee_resign_history_info');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeProbationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMPLOYEE_PROBATION_HISTORY'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_employee_id');
        	$table->enum('e_status' , [ config('constants.EXTEND_PROBATION') , config('constants.END_PROBATION') ] )->nullable();
        	$table->date('dt_start_date');
        	$table->date('dt_end_date')->nullable();
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
        Schema::dropIfExists(config('constants.EMPLOYEE_PROBATION_HISTORY'));
    }
}

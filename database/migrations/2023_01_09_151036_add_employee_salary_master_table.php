<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeSalaryMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMPLOYEE_SALARY_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_employee_id');
            $table->integer('i_salary_group_id');
            $table->enum('e_pf_by_employer',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->nullable();
            $table->integer('d_total_earning')->nullable();
            $table->integer('d_total_deduction')->nullable();
            $table->integer('d_net_pay_monthly')->nullable();
            $table->integer('d_net_pay_annually')->nullable();
            $table->enum('e_hold_salary',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->nullable();
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
        Schema::dropIfExists(config('constants.EMPLOYEE_SALARY_MASTER_TABLE'));
    }
}

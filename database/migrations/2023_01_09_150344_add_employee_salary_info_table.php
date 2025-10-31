<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeSalaryInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_employee_salary_id');
            $table->integer('i_salary_component_id');
            $table->double('d_amount')->nullable();
            $table->double('v_hold_salary_info')->nullable();
            $table->double('d_first_month_hold_amount')->nullable();
            $table->double('d_first_month_cut_amount')->nullable();
            $table->double('d_second_month_hold_amount')->nullable();
            $table->double('d_second_month_cut_amount')->nullable();
            $table->double('d_third_month_hold_amount')->nullable();
            $table->double('d_third_month_cut_amount')->nullable();
            $table->double('d_fourth_month_hold_amount')->nullable();
            $table->double('d_fourth_month_cut_amount')->nullable();
            $table->double('d_fifth_month_hold_amount')->nullable();
            $table->double('d_fifth_month_cut_amount')->nullable();
            $table->double('d_six_month_hold_amount')->nullable();
            $table->double('d_six_month_cut_amount')->nullable();
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
        Schema::dropIfExists(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'));
    }
}

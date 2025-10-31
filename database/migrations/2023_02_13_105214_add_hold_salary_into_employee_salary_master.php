<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoldSalaryIntoEmployeeSalaryMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.EMPLOYEE_SALARY_MASTER_TABLE'), function (Blueprint $table) {
	    	$table->double('d_first_month_hold_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_first_month_cut_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_second_month_hold_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_second_month_cut_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_third_month_hold_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_third_month_cut_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_fourth_month_hold_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_fourth_month_cut_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_fifth_month_hold_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_fifth_month_cut_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_six_month_hold_amount')->after('e_hold_salary')->nullable();
	    	$table->double('d_six_month_cut_amount')->after('e_hold_salary')->nullable();
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
    }
}

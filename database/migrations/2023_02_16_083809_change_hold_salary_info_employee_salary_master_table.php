<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeHoldSalaryInfoEmployeeSalaryMasterTable extends Migration
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
    		$table->renameColumn('d_first_month_cut_amount' , 'd_jan_cut_amount');
    		$table->renameColumn('d_first_month_hold_amount' , 'd_jan_hold_amount');
    		$table->renameColumn('d_second_month_cut_amount' , 'd_feb_cut_amount');
    		$table->renameColumn('d_second_month_hold_amount' , 'd_feb_hold_amount');
    		$table->renameColumn('d_third_month_cut_amount' , 'd_mar_cut_amount');
    		$table->renameColumn('d_third_month_hold_amount' , 'd_mar_hold_amount');
    		$table->renameColumn('d_fourth_month_cut_amount' , 'd_apr_cut_amount');
    		$table->renameColumn('d_fourth_month_hold_amount' , 'd_apr_hold_amount');
    		$table->renameColumn('d_fifth_month_cut_amount' , 'd_may_cut_amount');
    		$table->renameColumn('d_fifth_month_hold_amount' , 'd_may_hold_amount');
    		$table->double('d_jun_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_jun_cut_amount')->after('d_six_month_hold_amount')->default(0);
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

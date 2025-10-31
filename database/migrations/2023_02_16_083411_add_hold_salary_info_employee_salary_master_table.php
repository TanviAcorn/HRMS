<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoldSalaryInfoEmployeeSalaryMasterTable extends Migration
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
    		$table->double('d_jul_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_jul_cut_amount')->after('d_six_month_hold_amount')->default(0);
    		$table->double('d_aug_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_aug_cut_amount')->after('d_six_month_hold_amount')->default(0);
    		$table->double('d_sep_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_sep_cut_amount')->after('d_six_month_hold_amount')->default(0);
    		$table->double('d_oct_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_oct_cut_amount')->after('d_six_month_hold_amount')->default(0);
    		$table->double('d_nov_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_nov_cut_amount')->after('d_six_month_hold_amount')->default(0);
    		$table->double('d_dec_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_dec_cut_amount')->after('d_six_month_hold_amount')->default(0);
    		
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

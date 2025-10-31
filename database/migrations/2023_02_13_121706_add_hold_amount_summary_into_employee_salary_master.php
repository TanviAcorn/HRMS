<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoldAmountSummaryIntoEmployeeSalaryMaster extends Migration
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
    		$table->double('d_total_hold_amount')->after('d_six_month_hold_amount')->nullable();
    		$table->double('d_total_cut_amount')->after('d_six_month_hold_amount')->default(0);
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

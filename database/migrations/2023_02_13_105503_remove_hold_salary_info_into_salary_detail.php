<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveHoldSalaryInfoIntoSalaryDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'), function (Blueprint $table) {
    		$table->dropColumn('d_first_month_hold_amount');
    		$table->dropColumn('d_first_month_cut_amount');
    		$table->dropColumn('d_second_month_hold_amount');
    		$table->dropColumn('d_second_month_cut_amount');
    		$table->dropColumn('d_third_month_hold_amount');
    		$table->dropColumn('d_third_month_cut_amount');
    		$table->dropColumn('d_fourth_month_hold_amount');
    		$table->dropColumn('d_fourth_month_cut_amount');
    		$table->dropColumn('d_fifth_month_hold_amount');
    		$table->dropColumn('d_fifth_month_cut_amount');
    		$table->dropColumn('d_six_month_hold_amount');
    		$table->dropColumn('d_six_month_cut_amount');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalaryHoldInfoIntoSalaryTable extends Migration
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
    		$table->longText('v_hold_salary_info')->after('e_hold_salary')->nullable();
    		$table->dropColumn('d_six_month_cut_amount');
    		$table->dropColumn('d_six_month_hold_amount');
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

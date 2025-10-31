<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpectedReleaseOnHoldAmountInfoIntoEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
    		$table->datetime('dt_on_hold_expected_release_date')->after('e_hold_salary_payment_status')->nullable();
    		$table->datetime('dt_on_hold_release_date')->after('e_hold_salary_payment_status')->nullable();
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

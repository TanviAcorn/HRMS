<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnHoldSalaryInfoIntoSalaryMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
        	 $table->enum('e_hold_salary_status',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->after('e_assign_salary')->default(config('constants.SELECTION_NO'));
        	 $table->enum('e_hold_salary_payment_status',[config('constants.PENDING_STATUS'),config('constants.NOT_TO_PAY_STATUS'),config('constants.DONATED_STATUS'),config('constants.PAID_STATUS')])->after('e_hold_salary_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists(config('constants.EMPLOYEE_MASTER_TABLE'));
    }
}

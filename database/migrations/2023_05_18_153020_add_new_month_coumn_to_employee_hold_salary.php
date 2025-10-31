<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewMonthCoumnToEmployeeHoldSalary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(config("constants.EMPLOYEE_HOLD_SALARY_INFO"), function (Blueprint $table) {
            $table->date("dt_month")->after("i_employee_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config("constants.EMPLOYEE_HOLD_SALARY_INFO"), function (Blueprint $table) {
            //
        });
    }
}

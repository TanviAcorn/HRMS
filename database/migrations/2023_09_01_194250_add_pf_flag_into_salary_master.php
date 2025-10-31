<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPfFlagIntoSalaryMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.SALARY_MASTER_TABLE'), function (Blueprint $table) {
    		$table->enum('e_pf_deduction' , [config('constants.SELECTION_YES'),config('constants.SELECTION_NO')] )->after('dt_salary_month')->default(config('constants.SELECTION_NO'));
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

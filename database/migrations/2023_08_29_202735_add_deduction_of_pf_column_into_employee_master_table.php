<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeductionOfPfColumnIntoEmployeeMasterTable extends Migration
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
    		$table->enum('e_pf_deduction' , [config('constants.SELECTION_YES'),config('constants.SELECTION_NO')] )->after('e_assign_salary')->default(config('constants.SELECTION_NO'));
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
